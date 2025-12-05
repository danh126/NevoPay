<?php 

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Lấy toàn bộ người dùng
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Tìm người dùng theo ID
     */
    public function find(int $id): ?User
    {
        return User::findOrFail($id);
    }

    /**
     * Tìm người dùng theo email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Tìm người dùng theo phone
     */
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    /**
     * Tạo người dùng mới
     */
    public function create(array $data): ?User
    {
        return User::create($data);
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found with ID {$id}");
        }

        $user->update($data);
        return $user;
    }

    /**
     * Cập nhật password
     */
    public function updatePassword(int $id, string $newPassword): ?User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found with ID {$id}");
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return $user;
    }

    /**
     * Xóa người dùng
     */
    public function delete(int $id): bool
    {
        $user = $this->find($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User not found with ID {$id}");
        }

        return (bool) $user->delete();
    }

    /**
     * Kiểm tra user có đang active hay không
     */
    public function isActive(int $id): bool
    {
        $user = $this->find($id);
        return $user ? (bool) $user->is_active : false;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Bật xác thực hai yếu tố
     */
    public function enableTwoFactor(int $id): bool
    {
        $user = $this->find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found with ID {$id}");
        }

        $user->two_factor_enabled = true;

        return (bool) $user->save();
    }

    /**
     * Tắt xác thực hai yếu tố
     */
    public function disableTwoFactor(int $id): bool
    {
        $user = $this->find($id);

        if (!$user) {
            throw new ModelNotFoundException("User not found with ID {$id}");
        }

        $user->two_factor_enabled = false;

        return (bool) $user->save();
    }
}