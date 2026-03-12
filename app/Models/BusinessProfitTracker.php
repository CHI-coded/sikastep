<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfitTracker extends Model
{
    use HasFactory;

    protected $table = 'business_profit_tracker';

    protected $fillable = [
        'user_id',
        'total_income',
        'total_expense',
        'profit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}