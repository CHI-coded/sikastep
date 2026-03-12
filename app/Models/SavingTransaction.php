<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['saving_goal_id', 'amount', 'transaction_date'];

    public function savingGoal()
    {
        return $this->belongsTo(SavingGoal::class);
    }
}