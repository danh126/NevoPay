<?php 

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class WalletRepository implements WalletRepositoryInterface
{
    /**
     * Lấy toàn bộ ví.
     */
    public function all(): Collection
    {
        return Wallet::all();
    }

    /**
     * Tìm ví theo ID.
     */
    public function find(int $id): ?Wallet
    {
        return Wallet::findOrFail($id);
    }

    /**
     * Tìm ví theo số (mã ví).
     */
    public function findByWalletNumber(string $wallet_number): ?Wallet
    {
        return Wallet::where('wallet_number', $wallet_number)->firstOrFail();
    }

    /**
     * Tìm ví theo ID user
     */
    public function findByUserId(int $user_id): ?Wallet
    {
        return Wallet::where('user_id', $user_id)->firstOrFail();
    }

    /**
     * Lấy tất cả ví active.
     */
    public function getActiveWallets(): Collection
    {
        return Wallet::where('is_active', true)->get();
    }

    /**
     * Tạo ví.
     */
    public function create(array $data): ?Wallet
    {
        return Wallet::create($data);
    }

    /**
     * Cập nhật ví theo ID.
     */
    public function update(int $id, array $data): ?Wallet
    {
        $wallet = $this->find($id);

        if (!$wallet) {
            throw new ModelNotFoundException("Wallet not found with ID {$id}");
        }

        $wallet->update($data);
        return $wallet->fresh();
    }

    /**
     * Update balance với locking để tránh race condition.
     */
    public function updateBalance(int $id, float $amount): Wallet
    {
        $wallet = Wallet::where('id', $id)->lockForUpdate()->firstOrFail();
        $wallet->increment('balance', $amount);

        return $wallet->fresh();
    }
    
    /**
     * Deduct balance với validation.
     */
    public function deductBalance(int $id, float $amount): Wallet
    {
        $wallet = Wallet::where('id', $id)->lockForUpdate()->firstOrFail();
        
        if ($wallet->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        
        $wallet->decrement('balance', $amount);
        return $wallet->fresh();
    }

    /**
     * Xóa ví theo ID.
     */
    public function delete(int $id): bool
    {
        $wallet = $this->find($id);
        
        if (!$wallet) {
            throw new ModelNotFoundException("Wallet not found with ID {$id}");
        }

        return (bool) $wallet->delete();
    }

    /**
     * Kiểm tra ví có đang active hay không.
     */
    public function isActive(int $id): bool
    {
        return Wallet::where('id', $id)->where('is_active', true)->exists();
    }

    /**
     * Check balance đủ không.
     */
    public function hasSufficientBalance(int $id, float $amount): bool
    {
        return Wallet::where('id', $id)->where('balance', '>=', $amount)->exists();
    }

    /**
     * Check wallet owner
     */
    public function assertOwnedBy(int $walletId, int $userId): void
    {
        $isOwner = Wallet::where('id', $walletId)
        ->where('user_id', $userId)
        ->exists();

        if(!$isOwner){
            throw new InvalidArgumentException("You do not own this wallet.");
        }
    }
}