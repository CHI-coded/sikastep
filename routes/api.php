<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
        
        Auth::login($user, true);
        session(['user_id' => $user->id]);
        session(['user_name' => $user->name]);
        session()->save();
        
        DB::table('sessions')->where('id', session()->getId())->update(['user_id' => $user->id]);
        
        return response()->json(['user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Login endpoint
Route::post('/login', function(Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials, true)) {
        $user = Auth::user();
        session(['user_id' => $user->id]);
        session(['user_name' => $user->name]);
        session()->save();
        DB::table('sessions')->where('id', session()->getId())->update(['user_id' => $user->id]);
        
        return response()->json(['success' => true, 'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'is_admin' => $user->is_admin]]);
    }
    
    return response()->json(['error' => 'Invalid credentials'], 401);
});

// Logout endpoint
Route::post('/logout', function(Request $request) {
    session()->flush();
    Auth::logout();
    session()->regenerate();
    return response()->json(['message' => 'Logged out successfully']);
});

// Check session status
Route::get('/check-session', function() {
    $sessionId = session()->getId();
    $session = DB::table('sessions')->where('id', $sessionId)->first();
    
    return response()->json([
        'success' => true,
        'session_id' => $sessionId,
        'user_id_from_db' => $session ? $session->user_id : null,
        'user_id_from_session' => session('user_id'),
        'user_name_from_session' => session('user_name'),
        'is_authenticated' => Auth::check(),
        'auth_user_id' => Auth::id(),
        'is_admin' => Auth::check() ? Auth::user()->is_admin : false
    ]);
});

// ========== ADMIN ROUTES ==========
Route::middleware(['admin'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', function() {
        return response()->json([
            'users' => ['total' => DB::table('users')->count(), 'active' => DB::table('users')->where('status', 'active')->count(), 'admins' => DB::table('users')->where('is_admin', 1)->count()],
            'goals' => ['total' => DB::table('saving_goals')->count()],
            'finance' => ['total_savings' => DB::table('saving_transactions')->sum('amount'), 'total_loans_requested' => DB::table('loan_requests')->sum('amount')],
            'loans' => ['pending' => DB::table('loan_requests')->where('status', 'pending')->count()]
        ]);
    });
    
    Route::get('/users', function() {
        $users = DB::table('users')->select('id', 'name', 'email', 'is_admin', 'status', 'created_at')->orderBy('created_at', 'desc')->get();
        foreach ($users as $user) {
            $user->goal_count = DB::table('saving_goals')->where('user_id', $user->id)->count();
            $user->savings_total = DB::table('saving_transactions')->join('saving_goals', 'saving_transactions.saving_goal_id', '=', 'saving_goals.id')->where('saving_goals.user_id', $user->id)->sum('saving_transactions.amount');
        }
        return response()->json($users);
    });
    
    Route::put('/users/{id}/make-admin', function($id) {
        DB::table('users')->where('id', $id)->update(['is_admin' => 1]);
        return response()->json(['message' => 'User is now an admin']);
    });
    
    Route::put('/users/{id}/remove-admin', function($id) {
        DB::table('users')->where('id', $id)->update(['is_admin' => 0]);
        return response()->json(['message' => 'Admin privileges removed']);
    });
    
    Route::put('/users/{id}/suspend', function($id) {
        DB::table('users')->where('id', $id)->update(['status' => 'suspended']);
        return response()->json(['message' => 'User suspended']);
    });
    
    Route::put('/users/{id}/activate', function($id) {
        DB::table('users')->where('id', $id)->update(['status' => 'active']);
        return response()->json(['message' => 'User activated']);
    });
    
    Route::delete('/users/{id}', function($id) {
        DB::table('saving_goals')->where('user_id', $id)->delete();
        DB::table('credit_scores')->where('user_id', $id)->delete();
        DB::table('loan_requests')->where('user_id', $id)->delete();
        DB::table('business_transactions')->where('user_id', $id)->delete();
        DB::table('sessions')->where('user_id', $id)->delete();
        DB::table('users')->where('id', $id)->delete();
        return response()->json(['message' => 'User deleted']);
    });
    
    Route::get('/loans', function() {
        $loans = DB::table('loan_requests')
            ->leftJoin('users', 'loan_requests.user_id', '=', 'users.id')
            ->select('loan_requests.*', 'users.name as user_name')
            ->orderBy('loan_requests.created_at', 'desc')
            ->get();
        return response()->json($loans);
    });
    
    Route::put('/loans/{id}/approve', function($id) {
        DB::table('loan_requests')->where('id', $id)->update(['status' => 'approved']);
        return response()->json(['message' => 'Loan approved']);
    });
    
    Route::put('/loans/{id}/reject', function($id) {
        DB::table('loan_requests')->where('id', $id)->update(['status' => 'rejected']);
        return response()->json(['message' => 'Loan rejected']);
    });
    
    Route::get('/goals', function() {
        return response()->json(DB::table('saving_goals')->join('users', 'saving_goals.user_id', '=', 'users.id')->select('saving_goals.*', 'users.name as user_name')->orderBy('saving_goals.created_at', 'desc')->get());
    });
    
    Route::get('/transactions', function() {
        return response()->json(DB::table('business_transactions')->join('users', 'business_transactions.user_id', '=', 'users.id')->select('business_transactions.*', 'users.name as user_name')->orderBy('business_transactions.created_at', 'desc')->limit(100)->get());
    });
    
    Route::get('/logs', function() {
        return response()->json([
            'recent_users' => DB::table('users')->orderBy('created_at', 'desc')->limit(10)->get(),
            'recent_goals' => DB::table('saving_goals')->orderBy('created_at', 'desc')->limit(10)->get(),
            'recent_loans' => DB::table('loan_requests')->orderBy('created_at', 'desc')->limit(10)->get()
        ]);
    });
});

// API Resources
Route::apiResource('saving-goals', App\Http\Controllers\SavingGoalController::class);
Route::apiResource('saving-transactions', App\Http\Controllers\SavingTransactionController::class);
Route::apiResource('loan-requests', App\Http\Controllers\LoanRequestController::class);
Route::apiResource('credit-scores', App\Http\Controllers\CreditScoreController::class);
Route::apiResource('loan-repayments', App\Http\Controllers\LoanRepaymentController::class);
Route::apiResource('business-transactions', App\Http\Controllers\BusinessTransactionController::class);
Route::apiResource('business-profit', App\Http\Controllers\BusinessProfitTrackerController::class);