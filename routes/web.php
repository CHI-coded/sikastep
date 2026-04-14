<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Serve your frontend application for the root URL
Route::get('/', function () {
    return response()->file(public_path('index.html'));
});


Route::get('/test-session', function() {
    if (Auth::check()) {
        return response()->json([
            'logged_in' => true,
            'user_id' => Auth::id(),
            'session_id' => session()->getId()
        ]);
    }
    return response()->json(['logged_in' => false]);
});

// DO NOT add any catch-all routes here
// API routes are handled by routes/api.php
// Static files (CSS, JS, images) are handled by the web server