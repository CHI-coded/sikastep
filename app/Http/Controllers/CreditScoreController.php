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
            'score' => 'required|numeric|min:0|max:1000',
        ]);

        $score = CreditScore::create($validated);
        return response()->json($score, 201);
    }
}