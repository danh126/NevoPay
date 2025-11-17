<?php 

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Lấy toàn bộ người dùng.
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Tìm người dùng theo ID.
     */
    public function find(int $id): ?User
    {
        return User::findOrFail($id);
    }

    /**
     * Tìm người dùng theo email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Tạo người dùng mới.
     */
    public function create(array $data): ?User
    {
        try {
            return User::create($data);
        } catch (\Exception $e) {
            Log::error('UserRepository::create failed', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            return null;
        }
    }

    /**
     * Cập nhật thông tin người dùng.
     */
    public function update(int $id, array $data): ?User
    {
        try {
            $user = $this->find($id);
            if (!$user) {
                throw new ModelNotFoundException("User not found with ID {$id}");
            }

            $user->update($data);
            return $user->fresh();
        } catch (\Exception $e) {
            Log::error('UserRepository::update failed', [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'data' => $data,
            ]);
            return null;
        }
    }

    /**
     * Xóa người dùng.
     */
    public function delete(int $id): bool
    {
        try {
            $user = $this->find($id);
            if (!$user) {
                throw new ModelNotFoundException("User not found with ID {$id}");
            }

            return (bool) $user->delete();
        } catch (\Exception $e) {
            Log::error('UserRepository::delete failed', [
                'message' => $e->getMessage(),
                'user_id' => $id,
            ]);
            return false;
        }
    }

    /**
     * Kiểm tra user có đang active hay không.
     */
    public function isActive(int $id): bool
    {
        $user = $this->find($id);
        return $user ? (bool) $user->is_active : false;
    }

    /**
     * Kiểm tra email đã tồn tại chưa.
     */
    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}