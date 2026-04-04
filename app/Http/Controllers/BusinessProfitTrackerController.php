<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfitTracker;
use Illuminate\Http\Request;

class BusinessProfitTrackerController extends Controller
{
    public function index()
    {
        return BusinessProfitTracker::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
            'profit_date' => 'required|date',
        ]);

        $profit = BusinessProfitTracker::create($validated);
        return response()->json($profit, 201);
    }

    public function show($id)
    {
        return BusinessProfitTracker::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $profit = BusinessProfitTracker::findOrFail($id);

        $validated = $request->validate([
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
            'profit_date' => 'required|date',
        ]);

        $profit->update($validated);
        return response()->json($profit);
    }

    public function destroy($id)
    {
        BusinessProfitTracker::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}