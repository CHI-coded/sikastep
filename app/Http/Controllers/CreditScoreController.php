<?php

namespace App\Http\Controllers;

use App\Models\CreditScore;
use Illuminate\Http\Request;

class CreditScoreController extends Controller
{
    public function index()
    {
        return CreditScore::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'score' => 'required|numeric',
        ]);

        $score = CreditScore::create($validated);
        return response()->json($score, 201);
    }

    public function show($id)
    {
        return CreditScore::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $score = CreditScore::findOrFail($id);

        $validated = $request->validate([
            'score' => 'required|numeric',
        ]);

        $score->update($validated);
        return response()->json($score);
    }

    public function destroy($id)
    {
        CreditScore::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}