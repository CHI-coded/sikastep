<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoanRepayment;

class LoanRepaymentController extends Controller
{
    public function index()
    {
        return LoanRepayment::all();
    }

    public function store(Request $request)
    {
        return LoanRepayment::create($request->all());
    }

    public function show($id)
    {
        return LoanRepayment::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $repayment = LoanRepayment::findOrFail($id);
        $repayment->update($request->all());

        return $repayment;
    }

    public function destroy($id)
    {
        LoanRepayment::destroy($id);
        return response()->json(['message' => 'Repayment deleted']);
    }
}
