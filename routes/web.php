<?php

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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/



Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});


Route::resource('saving-goals', SavingGoalController::class);

Route::resource('saving-transactions', SavingTransactionController::class);

Route::resource('loan-requests', LoanRequestController::class);

Route::resource('credit-scores', CreditScoreController::class);

Route::resource('loan-repayments', LoanRepaymentController::class);

Route::resource('business-transactions', BusinessTransactionController::class);

Route::resource('business-profit', BusinessProfitTrackerController::class);
