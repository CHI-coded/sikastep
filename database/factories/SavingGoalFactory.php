<?php

namespace Database\Factories;

use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingGoalFactory extends Factory
{
    protected $model = SavingGoal::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->word() . ' Goal',
            'target_amount' => $this->faker->numberBetween(500, 5000),
            'deadline' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}
