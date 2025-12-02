<?php 

namespace App\Services;

use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletService
{
    public function __construct(protected WalletRepositoryInterface $walletRepository){}

    /**
     * Tạo ví mới cho user.
     */
    public function createForUser(int $userId, array $data = [])
    {
        $data['user_id'] = $userId;
        $data['balance'] = $data['balance'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return $this->walletRepository->create($data);
    }

    /**
     * Lấy thông tin ví theo ID.
     */
    public function getWallet(int $id)
    {
        try {
            return $this->walletRepository->find($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Wallet not found.");
        }
    }

    /**
     * Lấy ví theo user ID.
     */
    public function getByUserId(int $userId)
    {
        try {
            return $this->walletRepository->findByUserId($userId);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Wallet not found for user.");
        }
    }

    /**
     * Cập nhật thông tin ví (không phải balance).
     */
    public function update(int $walletId, array $data)
    {
        // Không cho cập nhật balance, wallet_number tại WalletService
        unset($data['balance'], $data['wallet_number']);

        return $this->walletRepository->update($walletId, $data);
    }

    /**
     * Kích hoạt / vô hiệu hóa ví.
     */
    public function toggleActive(int $walletId, bool $status)
    {
        return $this->walletRepository->update($walletId, [
            'is_active' => $status,
        ]);
    }

    /**
     * Xóa ví.
     */
    public function delete(int $walletId)
    {
        return $this->walletRepository->delete($walletId);
    }

    /**
     * Kiểm tra ví active.
     */
    public function isActive(int $walletId)
    {
        return $this->walletRepository->isActive($walletId);
    }

    /**
     * Lấy tất cả ví active.
     */
    public function getActiveWallets()
    {
        return $this->walletRepository->getActiveWallets();
    }

    /**
     * Lấy tất cả ví (admin).
     */
    public function getAll()
    {
        return $this->walletRepository->all();
    }

    /**
     * Tìm ví theo wallet number.
     */
    public function getByWalletNumber(string $walletNumber)
    {
        try {
            return $this->walletRepository->findByWalletNumber($walletNumber);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Wallet not found with this number.");
        }
    }
}