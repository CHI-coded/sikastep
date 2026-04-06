<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'target_amount', 'deadline'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }
}
