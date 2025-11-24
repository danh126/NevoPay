<?php

namespace App\Services;

use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    protected array $sensitiveKeys = [
        'password',
        'remember_token',
        'token',
        'wallet_number',
    ];

    public function __construct(
        protected AuditLogRepositoryInterface $auditLogRepository
    ) {}

    public function log(
        string $action,
        ?object $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?int $userId = null,
    ) {
        [$auditableType, $auditableId] = $this->resolveAuditable($model);

        return $this->auditLogRepository->create([
            'user_id'        => $userId ?? Auth::id(),
            'action'         => $action,
            'auditable_type' => $auditableType,
            'auditable_id'   => $auditableId,
            'old_values'     => $this->sanitize($oldValues),
            'new_values'     => $this->sanitize($newValues),
            'description'    => $description,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    protected function resolveAuditable(?object $model): array
    {
        if ($model instanceof Model) {
            return [$model->getMorphClass(), $model->getKey()];
        }

        return [null, null];
    }

    protected function sanitize(?array $data): ?array
    {
        if (!$data) return null;

        foreach ($data as $key => &$value) {
            if (in_array($key, $this->sensitiveKeys)) {
                unset($data[$key]);
                continue;
            }

            if (is_array($value)) {
                $value = $this->sanitize($value);
            }
        }

        return $data;
    }
}