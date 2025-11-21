<?php

namespace App\Repositories\Interfaces;

use App\Models\Wallet;
use Illuminate\Support\Collection;

interface WalletRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Wallet;
    public function findByWalletNumber(string $wallet_number): ?Wallet;
    public function findByUserId(int $user_id): ?Wallet;
    public function getActiveWallets(): Collection; 
    public function create(array $data): ?Wallet;
    public function update(int $id, array $data): ?Wallet;
    public function updateBalance(int $id, float $amount): ?Wallet;
    public function deductBalance(int $id, float $amount): ?Wallet;
    public function delete(int $id): bool;
    public function isActive(int $id): bool;
    public function hasSufficientBalance(int $id, float $amount): bool;
    public function assertOwnedBy(int $walletId, int $userId): void;
}
