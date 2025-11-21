<?php

namespace App\Services;

use App\Events\Transaction\TransactionCreated;
use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    public function __construct(protected TransactionRepositoryInterface $transactionRepository, 
    protected WalletRepositoryInterface $walletRepository, protected UserRepositoryInterface $userRepository){}

    /**
     * Deposit (nạp tiền)
     */
    public function deposit(string $walletNumber, float $amount, int $createdBy): Transaction
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $walletId = $this->resolveWalletIdFromNumber($walletNumber);

        $this->walletRepository->assertOwnedBy($walletId, $createdBy);

        return DB::transaction(function () use ($walletId, $amount, $createdBy) {

            // Tạo transaction pending
            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $walletId,
                    type: 'deposit',
                    amount: $amount,
                    createdBy: $createdBy,
                    status: 'pending',
                    senderWalletId: null,
                    receiverWalletId: null
                )
            );

            // Dispatch event
            DB::afterCommit(fn() => TransactionCreated::dispatch($transaction));

            return $transaction;
        });
    }

    /**
     * Withdraw (rút tiền)
     */
    public function withdraw(string $walletNumber, float $amount, int $createdBy): Transaction
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $walletId = $this->resolveWalletIdFromNumber($walletNumber);
        
        $this->walletRepository->assertOwnedBy($walletId, $createdBy);

        return DB::transaction(function () use ($walletId, $amount, $createdBy) {

            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $walletId,
                    type: 'withdraw',
                    amount: $amount,
                    createdBy: $createdBy,
                    status: 'pending',
                    senderWalletId: null,
                    receiverWalletId: null
                )
            );

            DB::afterCommit(fn() => TransactionCreated::dispatch($transaction));

            return $transaction;
        });
    }

    /**
     * Transfer (chuyển khoản)
     */
    public function transfer(string $fromwalletNumber, string $towalletNumber, float $amount, int $createdBy): Transaction
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $fromWalletId = $this->resolveWalletIdFromNumber($fromwalletNumber);
        $toWalletId   = $this->resolveWalletIdFromNumber($towalletNumber);

        $this->walletRepository->assertOwnedBy($fromWalletId, $createdBy);

        if ($fromWalletId === $toWalletId) {
            throw new InvalidArgumentException("Cannot transfer to the same wallet.");
        }

        return DB::transaction(function () use ($fromWalletId, $toWalletId, $amount, $createdBy) {

            $transaction = $this->transactionRepository->create(
                $this->buildPayload(
                    walletId: $fromWalletId,
                    type: 'transfer',
                    amount: $amount,
                    createdBy: $createdBy,
                    status: 'pending',
                    senderWalletId: $fromWalletId,
                    receiverWalletId: $toWalletId
                )
            );

            DB::afterCommit(fn() => TransactionCreated::dispatch($transaction));

            return $transaction;
        });
    }

    /**
     * Convert wallet_code → wallet_id & validate wallet active
     */
    private function resolveWalletIdFromNumber(string $walletNumber): int
    {
        $wallet = $this->walletRepository->findByWalletNumber($walletNumber);

        if (!$wallet) {
            throw new InvalidArgumentException("Wallet not found.");
        }

        if (!$wallet->is_active) {
            throw new InvalidArgumentException("Wallet is inactive.");
        }

        return $wallet->id;
    }

    /**
     * Validate amount
     */
    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0.");
        }
    }

    /**
     * Validate user active
     */
    private function validateUserActive(int $userId): void
    {
        if(!$this->userRepository->isActive($userId)){
            throw new InvalidArgumentException("User is inactive.");
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
        string $status,
        ?int $senderWalletId = null,
        ?int $receiverWalletId = null
    ): array {
        return [
            'wallet_id'          => $walletId,
            'type'               => $type,
            'amount'             => $amount,
            'status'             => $status,
            'description'        => ucfirst($type) . ' transaction',
            'sender_wallet_id'   => $senderWalletId,
            'receiver_wallet_id' => $receiverWalletId,
            'created_by'         => $createdBy,
            'completed_at'       => now(),
        ];
    }
}