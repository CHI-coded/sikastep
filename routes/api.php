<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\CreditScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }
    
    return response()->json(['error' => 'Invalid credentials'], 401);
});

// Logout endpoint
Route::post('/logout', function(Request $request) {
    return response()->json(['message' => 'Logged out successfully']);
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