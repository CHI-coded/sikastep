<?php

namespace App\Http\Controllers;

use App\Models\SavingTransaction;
use Illuminate\Http\Request;

class SavingTransactionController extends Controller
{
    public function index()
    {
        return SavingTransaction::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'saving_goal_id' => 'required|exists:saving_goals,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        $transaction = SavingTransaction::create($validated);
        return response()->json($transaction, 201);
    }
}
