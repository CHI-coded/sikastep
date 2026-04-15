<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CreditScore;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Register endpoint
Route::post('/register', function(Request $request) {
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:5|confirmed',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        // Auto-login
        Auth::login($user, true);
        
        session()->put('user_id', $user->id);
        session()->put('user_name', $user->name);
        session()->save();
        
        // Update sessions table
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update(['user_id' => $user->id]);
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin ?? false
            ]
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Login endpoint
Route::post('/login', function(Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials, true)) {
        $user = Auth::user();
        
        // Update last login time
        DB::table('users')->where('id', $user->id)->update(['last_login_at' => now()]);
        
        // Regenerate session ID for security
        session()->regenerate();
        
        // Store user data
        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => $user->is_admin ?? false
        ]);
        
        // Force save to database
        session()->save();
        
        // Update sessions table with user_id
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update(['user_id' => $user->id]);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin ?? false
            ]
        ]);
    }
    
    return response()->json(['error' => 'Invalid credentials'], 401);
});

// Logout endpoint
Route::post('/logout', function(Request $request) {
    // Get current session ID
    $sessionId = session()->getId();
    
    // Clear user_id from sessions table
    DB::table('sessions')
        ->where('id', $sessionId)
        ->update(['user_id' => null]);
    
    session()->flush();
    Auth::logout();
    session()->regenerate();
    
    return response()->json(['message' => 'Logged out successfully']);
});

// Check session status
Route::get('/check-session', function() {
    $sessionId = session()->getId();
    $session = DB::table('sessions')->where('id', $sessionId)->first();
    $user = Auth::user();
    
    return response()->json([
        'success' => true,
        'session_id' => $sessionId,
        'user_id_from_db' => $session ? $session->user_id : null,
        'user_id_from_session' => session('user_id'),
        'user_name_from_session' => session('user_name'),
        'is_authenticated' => Auth::check(),
        'auth_user_id' => Auth::id(),
        'is_admin' => $user ? ($user->is_admin ?? false) : false
    ]);
});

// Get authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Saving Goals
Route::apiResource('saving-goals', App\Http\Controllers\SavingGoalController::class);

// Saving Transactions
Route::apiResource('saving-transactions', App\Http\Controllers\SavingTransactionController::class);

// Loan Requests
Route::apiResource('loan-requests', App\Http\Controllers\LoanRequestController::class);

// Credit Scores
Route::apiResource('credit-scores', App\Http\Controllers\CreditScoreController::class);

// Loan Repayments
Route::apiResource('loan-repayments', App\Http\Controllers\LoanRepaymentController::class);

// Business Transactions
Route::apiResource('business-transactions', App\Http\Controllers\BusinessTransactionController::class);

// Business Profit Tracker
Route::apiResource('business-profit', App\Http\Controllers\BusinessProfitTrackerController::class);

// ==========================================
// COMPLETE ADMIN ROUTES
// ==========================================

Route::middleware(['admin'])->prefix('admin')->group(function () {
    
    // ========== DASHBOARD STATISTICS ==========
    Route::get('/dashboard', function() {
        $totalUsers = DB::table('users')->count();
        $activeUsers = DB::table('users')->where('status', 'active')->count();
        $suspendedUsers = DB::table('users')->where('status', 'suspended')->count();
        $totalAdmins = DB::table('users')->where('is_admin', 1)->count();
        
        $totalGoals = DB::table('saving_goals')->count();
        $completedGoals = DB::table('saving_goals')
            ->whereRaw('target_amount <= (SELECT COALESCE(SUM(amount), 0) FROM saving_transactions WHERE saving_transactions.saving_goal_id = saving_goals.id)')
            ->count();
        
        $totalSavings = DB::table('saving_transactions')->sum('amount');
        $totalLoanRequests = DB::table('loan_requests')->sum('amount');
        $pendingLoans = DB::table('loan_requests')->where('status', 'pending')->count();
        $approvedLoans = DB::table('loan_requests')->where('status', 'approved')->count();
        $rejectedLoans = DB::table('loan_requests')->where('status', 'rejected')->count();
        
        $totalTransactions = DB::table('business_transactions')->count();
        $totalIncome = DB::table('business_transactions')->where('amount', '>', 0)->sum('amount');
        $totalExpenses = DB::table('business_transactions')->where('amount', '<', 0)->sum('amount');
        
        // Monthly new users (last 6 months)
        $monthlyUsers = DB::table('users')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        return response()->json([
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
                'admins' => $totalAdmins
            ],
            'goals' => [
                'total' => $totalGoals,
                'completed' => $completedGoals,
                'completion_rate' => $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100, 2) : 0
            ],
            'finance' => [
                'total_savings' => $totalSavings,
                'total_loans_requested' => $totalLoanRequests,
                'total_income' => $totalIncome,
                'total_expenses' => abs($totalExpenses),
                'net_profit' => $totalIncome + $totalExpenses
            ],
            'loans' => [
                'pending' => $pendingLoans,
                'approved' => $approvedLoans,
                'rejected' => $rejectedLoans
            ],
            'monthly_users' => $monthlyUsers
        ]);
    });
    
    // ========== USER MANAGEMENT ==========
    
    // Get all users
    Route::get('/users', function() {
        $users = DB::table('users')
            ->leftJoin('credit_scores', 'users.id', '=', 'credit_scores.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.is_admin',
                'users.status',
                'users.created_at',
                'users.last_login_at',
                DB::raw('COALESCE(credit_scores.score, 0) as credit_score')
            )
            ->groupBy('users.id')
            ->orderBy('users.created_at', 'desc')
            ->get();
        
        // Add counts for each user
        foreach ($users as $user) {
            $user->goal_count = DB::table('saving_goals')->where('user_id', $user->id)->count();
            $user->savings_total = DB::table('saving_transactions')
                ->join('saving_goals', 'saving_transactions.saving_goal_id', '=', 'saving_goals.id')
                ->where('saving_goals.user_id', $user->id)
                ->sum('saving_transactions.amount');
            $user->loan_count = DB::table('loan_requests')->where('user_id', $user->id)->count();
        }
        
        return response()->json($users);
    });
    
    // Get single user details
    Route::get('/users/{id}', function($id) {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Get user's goals
        $goals = DB::table('saving_goals')
            ->leftJoin('saving_transactions', 'saving_goals.id', '=', 'saving_transactions.saving_goal_id')
            ->select(
                'saving_goals.*',
                DB::raw('COALESCE(SUM(saving_transactions.amount), 0) as saved_amount')
            )
            ->where('saving_goals.user_id', $id)
            ->groupBy('saving_goals.id')
            ->get();
        
        // Get user's loans
        $loans = DB::table('loan_requests')->where('user_id', $id)->get();
        
        // Get user's transactions
        $transactions = DB::table('business_transactions')->where('user_id', $id)->get();
        
        // Get credit score
        $creditScore = DB::table('credit_scores')->where('user_id', $id)->first();
        
        return response()->json([
            'user' => $user,
            'credit_score' => $creditScore,
            'goals' => $goals,
            'loans' => $loans,
            'transactions' => $transactions
        ]);
    });
    
    // Make user admin
    Route::put('/users/{id}/make-admin', function($id) {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        DB::table('users')->where('id', $id)->update(['is_admin' => 1]);
        
        return response()->json([
            'message' => 'User is now an admin',
            'user' => DB::table('users')->where('id', $id)->first()
        ]);
    });
    
    // Remove admin privileges
    Route::put('/users/{id}/remove-admin', function($id) {
        if ($id == auth()->id()) {
            return response()->json(['error' => 'Cannot remove your own admin privileges'], 400);
        }
        
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        DB::table('users')->where('id', $id)->update(['is_admin' => 0]);
        
        return response()->json(['message' => 'Admin privileges removed']);
    });
    
    // Suspend user
    Route::put('/users/{id}/suspend', function($id) {
        if ($id == auth()->id()) {
            return response()->json(['error' => 'Cannot suspend your own account'], 400);
        }
        
        DB::table('users')->where('id', $id)->update(['status' => 'suspended']);
        
        return response()->json(['message' => 'User suspended successfully']);
    });
    
    // Activate user
    Route::put('/users/{id}/activate', function($id) {
        DB::table('users')->where('id', $id)->update(['status' => 'active']);
        
        return response()->json(['message' => 'User activated successfully']);
    });
    
    // Delete user
    Route::delete('/users/{id}', function($id) {
        if ($id == auth()->id()) {
            return response()->json(['error' => 'Cannot delete your own account'], 400);
        }
        
        // Delete all related data
        DB::table('saving_goals')->where('user_id', $id)->delete();
        DB::table('saving_transactions')->whereIn('saving_goal_id', function($query) use ($id) {
            $query->select('id')->from('saving_goals')->where('user_id', $id);
        })->delete();
        DB::table('credit_scores')->where('user_id', $id)->delete();
        DB::table('loan_requests')->where('user_id', $id)->delete();
        DB::table('business_transactions')->where('user_id', $id)->delete();
        DB::table('sessions')->where('user_id', $id)->delete();
        DB::table('users')->where('id', $id)->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    });
    
    // ========== LOAN MANAGEMENT ==========
    
    // Get all loan requests
    Route::get('/loans', function() {
        $loans = DB::table('loan_requests')
            ->join('users', 'loan_requests.user_id', '=', 'users.id')
            ->select(
                'loan_requests.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderBy('loan_requests.created_at', 'desc')
            ->get();
        
        return response()->json($loans);
    });
    
    // Approve loan
    Route::put('/loans/{id}/approve', function($id) {
        $loan = DB::table('loan_requests')->where('id', $id)->first();
        if (!$loan) {
            return response()->json(['error' => 'Loan not found'], 404);
        }
        
        DB::table('loan_requests')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);
        
        return response()->json(['message' => 'Loan approved successfully']);
    });
    
    // Reject loan
    Route::put('/loans/{id}/reject', function($id) {
        DB::table('loan_requests')->where('id', $id)->update([
            'status' => 'rejected',
            'updated_at' => now()
        ]);
        
        return response()->json(['message' => 'Loan rejected']);
    });
    
    // ========== SYSTEM MANAGEMENT ==========
    
    // Get all saving goals (all users)
    Route::get('/goals', function() {
        $goals = DB::table('saving_goals')
            ->join('users', 'saving_goals.user_id', '=', 'users.id')
            ->leftJoin('saving_transactions', 'saving_goals.id', '=', 'saving_transactions.saving_goal_id')
            ->select(
                'saving_goals.*',
                'users.name as user_name',
                'users.email as user_email',
                DB::raw('COALESCE(SUM(saving_transactions.amount), 0) as saved_amount')
            )
            ->groupBy('saving_goals.id')
            ->orderBy('saving_goals.created_at', 'desc')
            ->get();
        
        return response()->json($goals);
    });
    
    // Get all transactions (all users)
    Route::get('/transactions', function() {
        $transactions = DB::table('business_transactions')
            ->join('users', 'business_transactions.user_id', '=', 'users.id')
            ->select('business_transactions.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('business_transactions.created_at', 'desc')
            ->limit(100)
            ->get();
        
        return response()->json($transactions);
    });
    
    // Get system logs (simple version)
    Route::get('/logs', function() {
        // Get recent activity
        $recentUsers = DB::table('users')->orderBy('created_at', 'desc')->limit(10)->get();
        $recentGoals = DB::table('saving_goals')->orderBy('created_at', 'desc')->limit(10)->get();
        $recentLoans = DB::table('loan_requests')->orderBy('created_at', 'desc')->limit(10)->get();
        $recentTransactions = DB::table('business_transactions')->orderBy('created_at', 'desc')->limit(10)->get();
        
        return response()->json([
            'recent_users' => $recentUsers,
            'recent_goals' => $recentGoals,
            'recent_loans' => $recentLoans,
            'recent_transactions' => $recentTransactions
        ]);
    });
});