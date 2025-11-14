<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Transaction;
    public function create(array $data): ?Transaction;
    public function update(int $id, array $data): ?Transaction;
    public function delete(int $id): bool;
    public function getByWallet(int $walletId, int $limit = 20): LengthAwarePaginator;
    public function getByType(string $type, int $limit = 20): LengthAwarePaginator;
    public function filter(array $filters, int $limit = 20): LengthAwarePaginator;
    public function existsByTransactionCode(string $transactionCode): bool;
    public function getWalletSummary(int $walletId): array;
}
