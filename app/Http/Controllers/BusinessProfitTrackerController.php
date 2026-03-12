<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfitTracker;
use Illuminate\Http\Request;

class BusinessProfitTrackerController extends Controller
{
    public function index()
    {
        return BusinessProfitTracker::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'business_transaction_id' => 'required|exists:business_transactions,id',
            'profit_amount' => 'required|numeric',
        ]);

        $profit = BusinessProfitTracker::create($validated);
        return response()->json($profit, 201);
    }
}
