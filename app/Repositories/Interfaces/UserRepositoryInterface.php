<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByPhone(string $phone): ?User;
    public function create(array $data): ?User;
    public function update(int $id, array $data): ?User;
    public function updatePassword(int $id, string $newPassword): ?User;
    public function delete(int $id): bool;
    public function isActive(int $id): bool;
    public function existsByEmail(string $email): bool;
    public function enableTwoFactor(int $id): bool;
    public function disableTwoFactor(int $id): bool;
}
