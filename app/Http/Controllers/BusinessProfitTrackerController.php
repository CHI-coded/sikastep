<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfitTracker;

class BusinessProfitTrackerController extends Controller
{
    public function index()
    {
        return BusinessProfitTracker::all();
    }

    public function store(Request $request)
    {
        return BusinessProfitTracker::create($request->all());
    }

    public function show($id)
    {
        return BusinessProfitTracker::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $profit = BusinessProfitTracker::findOrFail($id);
        $profit->update($request->all());

        return $profit;
    }

    public function destroy($id)
    {
        BusinessProfitTracker::destroy($id);
        return response()->json(['message' => 'Profit record deleted']);
    }
}
