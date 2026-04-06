<?php

namespace App\Http\Controllers;

use App\Models\SavingGoal;
use Illuminate\Http\Request;

class SavingGoalController extends Controller
{
    // Get all saving goals
    public function index()
    {
        return SavingGoal::all();
    }

    // Store new saving goal
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

    // Update saving goal
    public function update(Request $request, $id)
    {
        $goal = SavingGoal::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'target_amount' => 'sometimes|numeric|min:1',
            'deadline' => 'sometimes|date',
        ]);
        
        $goal->update($validated);
        return response()->json($goal);
    }

    // Delete saving goal
    public function destroy($id)
    {
        $goal = SavingGoal::findOrFail($id);
        $goal->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}