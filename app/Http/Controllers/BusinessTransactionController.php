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
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $transaction = BusinessTransaction::create($validated);
        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return BusinessTransaction::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $transaction = BusinessTransaction::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update($validated);
        return response()->json($transaction);
    }

    public function destroy($id)
    {
        BusinessTransaction::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}