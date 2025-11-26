<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    /** 
     * Lấy tất cả audit Logs.
     */
    public function all(): Collection
    {
        return AuditLog::all();
    }

    /**
     * Lấy audit Log theo ID.
     */
    public function find(int $id): ?AuditLog
    {
        return AuditLog::find($id);
    }

    /** 
     * Tạo mới audit Log.
     */
    public function create(array $data): AuditLog
    {
        try {
            return AuditLog::create($data);
        } catch (\Exception $e) {
            Log::error('AuditLogRepository::create failed', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /** 
     * Lọc audit Log.
     */
    public function filter(array $filters, int $limit = 20): LengthAwarePaginator
    {
        $query = AuditLog::query();

        $query->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']));
        $query->when(!empty($filters['action']), fn($q) => $q->where('action', $filters['action']));
        $query->when(!empty($filters['auditable_type']), fn($q) => $q->where('auditable_type', $filters['auditable_type']));
        $query->when(!empty($filters['auditable_id']), fn($q) => $q->where('auditable_id', $filters['auditable_id']));
        $query->when(!empty($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']));
        $query->when(!empty($filters['date_to']), fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']));

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }
}