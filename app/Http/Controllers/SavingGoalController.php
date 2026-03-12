<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingGoal;

class SavingGoalController extends Controller
{
    // Show all saving goals
    public function index()
    {
        return SavingGoal::all();
    }

    // Store a new saving goal
    public function store(Request $request)
    {
        return SavingGoal::create($request->all());
    }

    // Show a specific goal
    public function show($id)
    {
        return SavingGoal::findOrFail($id);
    }

    // Update a goal
    public function update(Request $request, $id)
    {
        $goal = SavingGoal::findOrFail($id);
        $goal->update($request->all());

        return $goal;
    }

    // Delete a goal
    public function destroy($id)
    {
        SavingGoal::destroy($id);
        return response()->json(['message' => 'Goal deleted']);
    }
}
