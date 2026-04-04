<?php

namespace App\Http\Controllers;

use App\Models\SavingGoal;
use Illuminate\Http\Request;

class SavingGoalController extends Controller
{
    public function index()
    {
        return SavingGoal::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date',
        ]);

        $goal = SavingGoal::create($validated);
        return response()->json($goal, 201);
    }

    public function show($id)
    {
        return SavingGoal::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $goal = SavingGoal::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date',
        ]);

        $goal->update($validated);
        return response()->json($goal);
    }

    public function destroy($id)
    {
        SavingGoal::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}