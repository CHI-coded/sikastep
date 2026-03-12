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

}
