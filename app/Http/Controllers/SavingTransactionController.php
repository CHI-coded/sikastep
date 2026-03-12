<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingTransaction;

class SavingTransactionController extends Controller
{
    public function index()
    {
        return SavingTransaction::all();
    }

    public function store(Request $request)
    {
        return SavingTransaction::create($request->all());
    }

    public function show($id)
    {
        return SavingTransaction::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $transaction = SavingTransaction::findOrFail($id);
        $transaction->update($request->all());

        return $transaction;
    }

    public function destroy($id)
    {
        SavingTransaction::destroy($id);
        return response()->json(['message' => 'Transaction deleted']);
    }
}
