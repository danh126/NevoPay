<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
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

// Audit Logs API
Route::prefix('admin')->middleware(['auth:sanctum', 'isAdmin'])->group(function() {
     Route::get('audit-logs', [AuditLogController::class, 'index']);
     Route::get('audit-logs/{id}', [AuditLogController::class, 'show']);
});

// User Authentication API
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:register')->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');
Route::post('verify-2fa', [AuthController::class, 'verifyTwoFactor'])->name('auth.verify-2fa');

Route::middleware(['auth:sanctum', 'throttle:api-auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
});
