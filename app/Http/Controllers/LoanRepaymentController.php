<?php

namespace App\Http\Controllers;

use App\Models\LoanRepayment;
use Illuminate\Http\Request;

class LoanRepaymentController extends Controller
{
    public function index()
    {
        return LoanRepayment::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_request_id' => 'required|exists:loan_requests,id',
            'amount' => 'required|numeric|min:1',
            'repayment_date' => 'required|date',
        ]);

        $repayment = LoanRepayment::create($validated);
        return response()->json($repayment, 201);
    }
}
