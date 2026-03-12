<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditScore;

class CreditScoreController extends Controller
{
    public function index()
    {
        return CreditScore::all();
    }

    public function store(Request $request)
    {
        return CreditScore::create($request->all());
    }

    public function show($id)
    {
        return CreditScore::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $score = CreditScore::findOrFail($id);
        $score->update($request->all());

        return $score;
    }

    public function destroy($id)
    {
        CreditScore::destroy($id);
        return response()->json(['message' => 'Credit score deleted']);
    }
}