<?php

namespace Database\Factories;

use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanRequestFactory extends Factory
{
    protected $model = LoanRequest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(100, 5000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'due_date' => $this->faker->dateTimeBetween('now', '+3 months'),
        ];
    }
}
