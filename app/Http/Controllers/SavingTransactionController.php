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
            'user_id' => 'required|exists:users,id',
        ]);

        $transaction = SavingTransaction::create($validated);
        return response()->json($transaction, 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = SavingTransaction::findOrFail($id);
        
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:1',
            'transaction_date' => 'sometimes|date',
        ]);
        
        $transaction->update($validated);
        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = SavingTransaction::findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}