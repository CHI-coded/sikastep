<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $table = 'loan_repayments';

    protected $fillable = [
        'loan_request_id',
        'amount',
        'repayment_date',
    ];

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class);
    }
}
