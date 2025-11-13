<?php 

namespace App\Repositories;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
        return Wallet::where('wallet_number', $wallet_number)->first();
    }

    /**
     * Tạo ví.
     */
    public function create(array $data): ?Wallet
    {
        try {
            return Wallet::create($data);
        } catch (\Exception $e) {
            Log::error('WalletRepository::create failed', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin ví.
     */
    public function update(int $id, array $data): ?Wallet
    {
        try {
            $wallet = $this->find($id);
            if (!$wallet) {
                throw new ModelNotFoundException("Wallet not found with ID {$id}");
            }

            $wallet->update($data);
            return $wallet;
        } catch (\Exception $e) {
            Log::error('WalletRepository::update failed', [
                'message' => $e->getMessage(),
                'wallet_id' => $id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Xóa ví.
     */
    public function delete(int $id): bool
    {
        try {
            $wallet = $this->find($id);
            if (!$wallet) {
                throw new ModelNotFoundException("Wallet not found with ID {$id}");
            }

            return (bool) $wallet->delete();
        } catch (\Exception $e) {
            Log::error('WalletRepository::delete failed', [
                'message' => $e->getMessage(),
                'wallet_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Kiểm tra ví có đang active hay không.
     */
    public function isActive(int $id): bool
    {
        return Wallet::where('id', $id)->where('is_active', true)->exits();
    }
}