<?php

namespace Database\Factories;

use App\Models\SavingTransaction;
use App\Models\SavingGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingTransactionFactory extends Factory
{
    protected $model = SavingTransaction::class;

    public function definition()
    {
        return [
            'saving_goal_id' => SavingGoal::factory(),
            'amount' => $this->faker->numberBetween(50, 500),
            'transaction_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
