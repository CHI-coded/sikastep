<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessTransaction;

class BusinessTransactionController extends Controller
{
    public function index()
    {
        return BusinessTransaction::all();
    }

    public function store(Request $request)
    {
        return BusinessTransaction::create($request->all());
    }

    public function show($id)
    {
        return BusinessTransaction::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $transaction = BusinessTransaction::findOrFail($id);
        $transaction->update($request->all());

        return $transaction;
    }

    public function destroy($id)
    {
        BusinessTransaction::destroy($id);
        return response()->json(['message' => 'Business transaction deleted']);
    }
}
