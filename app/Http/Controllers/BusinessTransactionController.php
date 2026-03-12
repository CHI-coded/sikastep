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
}
