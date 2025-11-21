<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Gửi thông báo khi giao dịch thành công
     */
    public function sendTransactionSuccess(Transaction $transaction): void
    {
        Log::info("Sent transaction success notification", [
            'transaction_code' => $transaction->transaction_code,
            'type'           => $transaction->type,
            'amount'         => $transaction->amount,
            'wallet_id'      => $transaction->wallet_id,
        ]);
    }

    /**
     * Gửi thông báo khi giao dịch thất bại
     */
    public function sendTransactionFailed(Transaction $transaction, string $reason): void
    {
        Log::info("Sent transaction failed notification", [
            'transaction_code' => $transaction->transaction_code,
            'type'           => $transaction->type,
            'amount'         => $transaction->amount,
            'wallet_id'      => $transaction->wallet_id,
            'reason'         => $reason,
        ]);
    }
}