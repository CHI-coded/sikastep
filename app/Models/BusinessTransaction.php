<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'description', 'amount', 'transaction_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessProfitTrackers()
    {
        return $this->hasMany(BusinessProfitTracker::class);
    }
}