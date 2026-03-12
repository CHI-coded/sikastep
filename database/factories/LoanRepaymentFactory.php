<?php

namespace Database\Factories;

use App\Models\LoanRepayment;
use App\Models\LoanRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanRepaymentFactory extends Factory
{
    protected $model = LoanRepayment::class;

    public function definition()
    {
        return [
            'loan_request_id' => LoanRequest::factory(),
            'amount' => $this->faker->numberBetween(50, 1000),
            'repayment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
