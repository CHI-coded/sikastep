<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\SavingTransactionController;
use App\Http\Controllers\LoanRequestController;
use App\Http\Controllers\CreditScoreController;
use App\Http\Controllers\LoanRepaymentController;
use App\Http\Controllers\BusinessTransactionController;
use App\Http\Controllers\BusinessProfitTrackerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by Laravel and are automatically assigned
| the "api" middleware group.
|
*/


// Get authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Saving Goals
Route::apiResource('saving-goals', SavingGoalController::class);

// Saving Transactions
Route::apiResource('saving-transactions', SavingTransactionController::class);

// Loan Requests
Route::apiResource('loan-requests', LoanRequestController::class);

// Credit Scores
Route::apiResource('credit-scores', CreditScoreController::class);

// Loan Repayments
Route::apiResource('loan-repayments', LoanRepaymentController::class);

// Business Transactions
Route::apiResource('business-transactions', BusinessTransactionController::class);

// Business Profit Tracker
Route::apiResource('business-profit', BusinessProfitTrackerController::class);