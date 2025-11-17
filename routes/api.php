<?php

use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Wallet API
Route::apiResource('wallets', WalletController::class);

Route::get('/users/{user}/wallet', [WalletController::class, 'showByUser'])
     ->name('wallets.by-user');

Route::patch('/wallets/{wallet}/active', [WalletController::class, 'updateActive'])
     ->name('wallets.update-active');