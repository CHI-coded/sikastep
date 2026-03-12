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
            'status' => 'required|string|max:50',
            'due_date' => 'required|date',
        ]);

        $loan = LoanRequest::create($validated);
        return response()->json($loan, 201);
    }
}
