<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use Illuminate\Http\Request;

class LoanRequestController extends Controller
{
    public function index()
    {
        return LoanRequest::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'status' => 'required|string',
        ]);

        $loan = LoanRequest::create($validated);
        return response()->json($loan, 201);
    }

    public function show($id)
    {
        return LoanRequest::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $loan = LoanRequest::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'status' => 'required|string',
        ]);

        $loan->update($validated);
        return response()->json($loan);
    }

    public function destroy($id)
    {
        LoanRequest::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}