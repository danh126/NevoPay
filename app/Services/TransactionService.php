<?php

namespace App\Services;

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
    public function deposit(string $walletNumber, float $amount, int $createdBy): array
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $walletId = $this->resolveWalletIdFromNumber($walletNumber);

        if($this->getOwnerId($walletId) !== $createdBy){
            throw new InvalidArgumentException("You do not own this wallet.");
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
     * Withdraw (rút tiền)
     */
    public function withdraw(string $walletNumber, float $amount, int $createdBy): array
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $walletId = $this->resolveWalletIdFromNumber($walletNumber);
        
        if($this->getOwnerId($walletId) !== $createdBy){
            throw new InvalidArgumentException("You do not own this wallet.");
        }

        return DB::transaction(function () use ($walletId, $amount, $createdBy) {

            if (!$this->walletRepository->hasSufficientBalance($walletId, $amount)) {
                throw new InvalidArgumentException("Insufficient balance.");
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
     * Transfer (chuyển khoản)
     */
    public function transfer(string $fromwalletNumber, string $towalletNumber, float $amount, int $createdBy): array
    {
        $this->validateUserActive($createdBy);
        $this->validateAmount($amount);

        $fromWalletId = $this->resolveWalletIdFromNumber($fromwalletNumber);
        $toWalletId   = $this->resolveWalletIdFromNumber($towalletNumber);

        if($this->getOwnerId($fromWalletId) !== $createdBy){
            throw new InvalidArgumentException("You do not own this wallet.");
        }

        if ($fromWalletId === $toWalletId) {
            throw new InvalidArgumentException("Cannot transfer to the same wallet.");
        }

        return DB::transaction(function () use ($fromWalletId, $toWalletId, $amount, $createdBy) {

            if (!$this->walletRepository->hasSufficientBalance($fromWalletId, $amount)) {
                throw new InvalidArgumentException("Insufficient balance.");
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
     * Check owner wallet
     */
    private function getOwnerId(int $walletId): int
    {
        $wallet = $this->walletRepository->find($walletId);

        if(!$wallet){
            throw new InvalidArgumentException("Wallet not found.");
        }

        return $wallet->user_id;
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
            'status'             => 'completed',
            'description'        => ucfirst($type) . ' transaction',
            'sender_wallet_id'   => $senderWalletId,
            'receiver_wallet_id' => $receiverWalletId,
            'created_by'         => $createdBy,
            'completed_at'       => now(),
        ];
    }
}