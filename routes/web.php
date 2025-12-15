<?php

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Test layout
Route::get('/test', function () {
    return view('admin.home.index');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/preview-mail', function () {
    return new OtpMail('123456', 15);
});
