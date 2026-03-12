<?php

namespace Database\Factories;

use App\Models\BusinessTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessTransactionFactory extends Factory
{
    protected $model = BusinessTransaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->numberBetween(100, 5000),
            'transaction_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
