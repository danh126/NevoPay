<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Lấy tất cả giao dịch.
     */
    public function all(): Collection
    {
        return Transaction::all();
    }

    /**
     * Lấy giao dịch theo ID.
     */
    public function find(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    /**
     * Tạo mới giao dịch.
     */
    public function create(array $data): ?Transaction
    {
        try {
            return Transaction::create($data);
        } catch (\Exception $e) {
            Log::error('TransactionRepository::create failed', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Cập nhật giao dịch theo ID.
     */
    public function update(int $id, array $data): ?Transaction
    {
        try {
            $transaction = $this->find($id);
            if (!$transaction) {
                throw new ModelNotFoundException("Transaction not found with ID {$id}");
            }

            $transaction->update($data);
            return $transaction->fresh();
        } catch (\Exception $e) {
            Log::error('TransactionRepository::update failed', [
                'message' => $e->getMessage(),
                'transaction_id' => $id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Xóa giao dịch theo ID.
     */
    public function delete(int $id): bool
    {
        try {
            $transaction = $this->find($id);
            if (!$transaction) {
                throw new ModelNotFoundException("Transaction not found with ID {$id}");
            }

            return (bool) $transaction->delete();
        } catch (\Exception $e) {
            Log::error('TransactionRepository::delete failed', [
                'message' => $e->getMessage(),
                'transaction_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Lấy giao dịch theo ID ví.
     */
    public function getByWallet(int $walletId, int $limit = 20): LengthAwarePaginator
    {
        return Transaction::where('sender_wallet_id', $walletId)
            ->orWhere('receiver_wallet_id', $walletId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /** 
     * Lấy giao dịch theo loại (deposit, withdraw, transfer).
     */
    public function getByType(string $type, int $limit = 20): LengthAwarePaginator
    {
        return Transaction::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Lọc giao dịch.
     */
    public function filter(array $filters, int $limit = 20): LengthAwarePaginator
    {
        $query = Transaction::query()
        ->when($filters['wallet_id'] ?? null, function ($q, $walletId) {
            $q->where(function ($q) use ($walletId) {
                $q->where('sender_wallet_id', $walletId)
                  ->orWhere('receiver_wallet_id', $walletId);
            });
        })
        ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
        ->when($filters['min_amount'] ?? null, fn($q, $min) => $q->where('amount', '>=', $min))
        ->when($filters['max_amount'] ?? null, fn($q, $max) => $q->where('amount', '<=', $max))
        ->when($filters['date_from'] ?? null, fn($q, $dateFrom) => $q->whereDate('created_at', '>=', $dateFrom))
        ->when($filters['date_to'] ?? null, fn($q, $dateTo) => $q->whereDate('created_at', '<=', $dateTo));

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    /** 
     * Kiểm tra mã giao dịch duy nhất.
     */
    public function existsByTransactionCode(string $transactionCode): bool
    {
        return Transaction::where('transaction_code', $transactionCode)->exists();
    }

    /**
     * Lấy tổng tiền ra/vào của ví.
     */
    public function getWalletSummary(int $walletId): array
    {
        $totalReceived = Transaction::where('receiver_wallet_id', $walletId)
            ->sum('amount');

        $totalSent = Transaction::where('sender_wallet_id', $walletId)
            ->sum('amount');

        return [
            'total_received' => $totalReceived,
            'total_sent' => $totalSent,
            'balance' => $totalReceived - $totalSent,
        ];
    }
}