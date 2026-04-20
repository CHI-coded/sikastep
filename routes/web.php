<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(public_path('index.html'));
});

Route::get('/admin-login', function () {
    return response()->file(public_path('admin-login.html'));
});

Route::get('/admin-panel', function () {
    return response()->file(public_path('admin-panel.html'));
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