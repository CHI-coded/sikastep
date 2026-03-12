<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTransaction extends Model
{
    use HasFactory;

    protected $table = 'business_transactions';

    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'type',
        'transaction_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}