<?php

namespace App\Repositories\Interfaces;

use App\Models\Wallet;
use Illuminate\Support\Collection;

interface WalletRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Wallet;
    public function findByWalletNumber(string $wallet_number): ?Wallet; 
    public function create(array $data): ?Wallet;
    public function update(int $id, array $data): ?Wallet;
    public function delete(int $id): bool;
    public function isActive(int $id): bool;
}
