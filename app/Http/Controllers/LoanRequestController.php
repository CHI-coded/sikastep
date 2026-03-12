<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoanRequest;

class LoanRequestController extends Controller
{
    public function index()
    {
        return LoanRequest::all();
    }

    public function store(Request $request)
    {
        return LoanRequest::create($request->all());
    }

    public function show($id)
    {
        return LoanRequest::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $loan = LoanRequest::findOrFail($id);
        $loan->update($request->all());

        return $loan;
    }

    public function destroy($id)
    {
        LoanRequest::destroy($id);
        return response()->json(['message' => 'Loan request deleted']);
    }
}
