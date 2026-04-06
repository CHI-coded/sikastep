<?php

namespace App\Http\Controllers;

use App\Models\BusinessTransaction;
use Illuminate\Http\Request;

class BusinessTransactionController extends Controller
{
    public function index()
    {
        return BusinessTransaction::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $transaction = BusinessTransaction::create($validated);
        return response()->json($transaction, 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = BusinessTransaction::findOrFail($id);
        
        $validated = $request->validate([
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric',
            'transaction_date' => 'sometimes|date',
        ]);
        
        $transaction->update($validated);
        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = BusinessTransaction::findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}