<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfitTracker extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'business_transaction_id', 'profit_amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessTransaction()
    {
        return $this->belongsTo(BusinessTransaction::class);
    }
}