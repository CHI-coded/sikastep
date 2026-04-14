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
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
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
        
        // Regenerate session ID for security
        session()->regenerate();
        
        // Store user data
        session([
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);
        
        // Force save to database
        session()->save();
        
        // Set cookie explicitly
        cookie()->queue(cookie('laravel_session', session()->getId(), 120, '/', '127.0.0.1', false, true, false, 'lax'));
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ])->withCookie(cookie('laravel_session', session()->getId(), 120, '/', '127.0.0.1', false, true, false, 'lax'));
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
        'auth_user_id' => Auth::id()
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