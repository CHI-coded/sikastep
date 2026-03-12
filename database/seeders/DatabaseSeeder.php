<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SavingGoal;
use App\Models\SavingTransaction;
use App\Models\LoanRequest;
use App\Models\LoanRepayment;
use App\Models\CreditScore;
use App\Models\BusinessTransaction;
use App\Models\BusinessProfitTracker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 5 users, each with related data
        User::factory()
            ->count(5)
            ->has(
                SavingGoal::factory()
                    ->count(2)
                    ->has(SavingTransaction::factory()->count(3), 'savingTransactions') // SavingGoal -> SavingTransactions
            )
            ->has(
                LoanRequest::factory()
                    ->count(2)
                    ->has(LoanRepayment::factory()->count(2), 'loanRepayments') // LoanRequest -> LoanRepayments
            )
            ->has(
                CreditScore::factory()->count(1), // User -> CreditScore
            )
            ->has(
                BusinessTransaction::factory()
                    ->count(2)
                    ->has(BusinessProfitTracker::factory()->count(1), 'businessProfitTrackers') // BusinessTransaction -> BusinessProfitTracker
            )
            ->create();
    }
}
