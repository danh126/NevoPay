<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Wallets API
Route::apiResource('wallets', WalletController::class);

Route::get('/users/{user}/wallet', [WalletController::class, 'showByUser'])
     ->name('wallets.by-user');

Route::patch('/wallets/{wallet}/active', [WalletController::class, 'updateActive'])
     ->name('wallets.update-active');

// Transactions API
Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {
    Route::post('deposit', [TransactionController::class, 'deposit'])->name('transactions.deposit');
    Route::post('withdraw', [TransactionController::class, 'withdraw'])->name('transactions.withdraw');
    Route::post('transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');
});