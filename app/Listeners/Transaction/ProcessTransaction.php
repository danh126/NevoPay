<?php

namespace App\Listeners\Transaction;

use App\Events\Transaction\TransactionCompleted;
use App\Events\Transaction\TransactionCreated;
use App\Events\Transaction\TransactionFailed;
use App\Models\Transaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessTransaction
{
    /**
     * Create the event listener.
     */
    public function __construct(protected WalletRepositoryInterface $walletRepository){}

    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        // Chỉ xử lý giao dịch pending
        if($transaction->status !== Transaction::STATUS_PENDING){
            return;
        }

        try {
            DB::transaction(function () use ($transaction) {

                match ($transaction->type) {
                    
                    Transaction::TYPE_DEPOSIT => $this->handleDeposit($transaction),

                    Transaction::TYPE_WITHDRAW => $this->handleWithdraw($transaction),

                    Transaction::TYPE_TRANSFER => $this->handleTransfer($transaction),

                    default => throw new \RuntimeException("Invalid transaction type"),
                };

                // Cập nhật trạng thái thành công
                $transaction->update([
                    'status' => Transaction::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

                DB::afterCommit(fn() => event(new TransactionCompleted($transaction)));
            });

        } catch (\Throwable $e) {

            Log::error("Transaction failed", [
                'transaction_id' => $transaction->id,
                'type'           => $transaction->type,
                'amount'         => $transaction->amount,
                'message'        => $e->getMessage(),
            ]);

            $transaction->update([
                'status' => Transaction::STATUS_FAILED,
            ]);

            DB::afterCommit(fn() => event(new TransactionFailed($transaction->fresh(), $e->getMessage())));

            return;
        }
    }

    private function handleDeposit($transaction)
    {
        $this->walletRepository->updateBalance(
            $transaction->wallet_id,
            $transaction->amount
        );
    }

    private function handleWithdraw($transaction)
    {
        if (!$this->walletRepository->hasSufficientBalance(
            $transaction->wallet_id,
            $transaction->amount
        )) {
            throw new \Exception('Insufficient balance');
        }

        $this->walletRepository->deductBalance(
            $transaction->wallet_id,
            $transaction->amount
        );
    }

    private function handleTransfer($transaction)
    {
        if (!$this->walletRepository->hasSufficientBalance(
            $transaction->sender_wallet_id,
            $transaction->amount
        )) {
            throw new \Exception('Insufficient balance');
        }

        // Trừ ví gửi
        $this->walletRepository->deductBalance(
            $transaction->sender_wallet_id,
            $transaction->amount
        );

        // Cộng ví nhận
        $this->walletRepository->updateBalance(
            $transaction->receiver_wallet_id,
            $transaction->amount
        );
    }
}
