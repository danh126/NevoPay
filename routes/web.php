<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Test layout
Route::get('/test', function () {
    return view('home.index');
});
