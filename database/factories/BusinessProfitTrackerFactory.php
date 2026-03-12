<?php

namespace Database\Factories;

use App\Models\BusinessProfitTracker;
use App\Models\BusinessTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessProfitTrackerFactory extends Factory
{
    protected $model = BusinessProfitTracker::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'business_transaction_id' => BusinessTransaction::factory(),
            'profit_amount' => $this->faker->numberBetween(50, 2000),
        ];
    }
}
