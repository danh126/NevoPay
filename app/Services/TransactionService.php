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

        $payload = $this->buildPayload(
            walletId: $walletId,
            type: Transaction::TYPE_DEPOSIT,
            amount: $amount,
            createdBy: $createdBy,
            senderWalletId: null,
            receiverWalletId: null
        );

        return $this->createPendingTransaction($payload);
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

        $payload = $this->buildPayload(
            walletId: $walletId,
            type: Transaction::TYPE_WITHDRAW,
            amount: $amount,
            createdBy: $createdBy,
            senderWalletId: null,
            receiverWalletId: null
        );

        return $this->createPendingTransaction($payload);
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

        $payload = $this->buildPayload(
            walletId: $fromWalletId,
            type: Transaction::TYPE_TRANSFER,
            amount: $amount,
            createdBy: $createdBy,
            senderWalletId: $fromWalletId,
            receiverWalletId: $toWalletId
        );

        return $this->createPendingTransaction($payload);
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
     * Create pending transaction
     */
    private function createPendingTransaction(array $payload): Transaction
    {
        return DB::transaction(function () use ($payload) {

            $transaction = $this->transactionRepository->create($payload);

            DB::afterCommit(fn() => TransactionCreated::dispatch($transaction));

            return $transaction;
        });
    }

    /**
     * Transaction payload builder
     */
    private function buildPayload(
        int $walletId,
        string $type,
        float $amount,
        int $createdBy,
        ?int $senderWalletId = null,
        ?int $receiverWalletId = null
    ): array {
        return [
            'wallet_id'          => $walletId,
            'type'               => $type,
            'amount'             => $amount,
            'status'             => Transaction::STATUS_PENDING,
            'description'        => ucfirst($type) . ' transaction',
            'sender_wallet_id'   => $senderWalletId,
            'receiver_wallet_id' => $receiverWalletId,
            'created_by'         => $createdBy,
            'completed_at'       => null,
        ];
    }
}