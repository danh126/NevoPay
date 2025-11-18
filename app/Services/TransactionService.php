<?php

namespace App\Services;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    public function __construct(protected TransactionRepositoryInterface $transactionRepository, 
    protected WalletRepositoryInterface $walletRepository){}

    /**
     * Deposit
     */
    public function deposit(string $walletCode, float $amount, int $createdBy): array
    {
        $walletId = $this->resolveWalletIdFromCode($walletCode);

        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0.");
        }

        return DB::transaction(function () use ($walletId, $amount, $createdBy) {

            $wallet = $this->walletRepository->updateBalance($walletId, $amount);

            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $walletId,
                    type: 'deposit',
                    amount: $amount,
                    createdBy: $createdBy
                )
            );

            return [
                'wallet' => $wallet,
                'transaction' => $transaction,
            ];
        });
    }

    /**
     * Withdraw
     */
    public function withdraw(string $walletCode, float $amount, int $createdBy): array
    {
        $walletId = $this->resolveWalletIdFromCode($walletCode);

        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0.");
        }

        return DB::transaction(function () use ($walletId, $amount, $createdBy) {

            if (!$this->walletRepository->hasSufficientBalance($walletId, $amount)) {
                throw new \Exception("Insufficient balance.");
            }

            $wallet = $this->walletRepository->deductBalance($walletId, $amount);

            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $walletId,
                    type: 'withdraw',
                    amount: $amount,
                    createdBy: $createdBy
                )
            );

            return [
                'wallet' => $wallet,
                'transaction' => $transaction,
            ];
        });
    }

    /**
     * Transfer
     */
    public function transfer(string $fromWalletCode, string $toWalletCode, float $amount, int $createdBy): array
    {
        $fromWalletId = $this->resolveWalletIdFromCode($fromWalletCode);
        $toWalletId   = $this->resolveWalletIdFromCode($toWalletCode);

        if ($fromWalletId === $toWalletId) {
            throw new InvalidArgumentException("Cannot transfer to the same wallet.");
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0.");
        }

        return DB::transaction(function () use ($fromWalletId, $toWalletId, $amount, $createdBy) {

            if (!$this->walletRepository->hasSufficientBalance($fromWalletId, $amount)) {
                throw new \Exception("Insufficient balance.");
            }

            $sender   = $this->walletRepository->deductBalance($fromWalletId, $amount);
            $receiver = $this->walletRepository->updateBalance($toWalletId, $amount);

            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $fromWalletId,
                    type: 'transfer',
                    amount: $amount,
                    createdBy: $createdBy,
                    senderWalletId: $fromWalletId,
                    receiverWalletId: $toWalletId
                )
            );

            return [
                'sender' => $sender,
                'receiver' => $receiver,
                'transaction' => $transaction,
            ];
        });
    }

    /**
     * Convert wallet_code → wallet_id
     */
    private function resolveWalletIdFromCode(string $walletCode): int
    {
        try {
            $wallet = $this->walletRepository->findByWalletNumber($walletCode);
            return $wallet->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \InvalidArgumentException("Wallet code '{$walletCode}' is invalid.");
        }
    }

    /**
     * Transaction payload builder
     */
    private function buildPayload(
        int $walletId,
        string $type,
        float $amount,
        int $createdBy,
        int $senderWalletId = null,
        int $receiverWalletId = null
    ): array {
        return [
            'wallet_id'          => $walletId,
            'type'               => $type,
            'amount'             => $amount,
            'status'             => 'completed',
            'description'        => ucfirst($type) . ' transaction',
            'sender_wallet_id'   => $senderWalletId,
            'receiver_wallet_id' => $receiverWalletId,
            'created_by'         => $createdBy,
            'completed_at'       => now(),
        ];
    }
}