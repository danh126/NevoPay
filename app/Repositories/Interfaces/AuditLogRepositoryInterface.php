<?php

namespace App\Repositories\Interfaces;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?AuditLog;
    public function create(array $data): ?AuditLog;
    public function filter(array $filters, int $limit = 20): LengthAwarePaginator;
}
