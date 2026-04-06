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
            'description' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'due_date' => 'required|date',
        ]);

        $loan = LoanRequest::create($validated);
        return response()->json($loan, 201);
    }

    public function update(Request $request, $id)
    {
        $loan = LoanRequest::findOrFail($id);
        
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:1',
            'status' => 'sometimes|string|max:50',
            'due_date' => 'sometimes|date',
        ]);
        
        $loan->update($validated);
        return response()->json($loan);
    }

    public function destroy($id)
    {
        $loan = LoanRequest::findOrFail($id);
        $loan->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}