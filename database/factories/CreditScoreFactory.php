<?php

namespace Database\Factories;

use App\Models\CreditScore;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditScoreFactory extends Factory
{
    protected $model = CreditScore::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'score' => $this->faker->numberBetween(300, 850),
        ];
    }
}