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
            'amount_paid' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $repayment = LoanRepayment::create($validated);
        return response()->json($repayment, 201);
    }

    public function show($id)
    {
        return LoanRepayment::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $repayment = LoanRepayment::findOrFail($id);

        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $repayment->update($validated);
        return response()->json($repayment);
    }

    public function destroy($id)
    {
        LoanRepayment::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}