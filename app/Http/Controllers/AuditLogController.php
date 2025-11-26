<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\AuditLogIndexRequest;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;

class AuditLogController extends Controller
{
    public function __construct(protected AuditLogRepositoryInterface $auditLogRepository){}

    /**
     * GET /audit-logs
     * Filter + paginate
     */
    public function index(AuditLogIndexRequest $request)
    {
        $filters = $request->filters();
        $limit = $request->get('limit', 20);

        $logs = $this->auditLogRepository->filter($filters, $limit);

        return ApiResponse::paginated($logs);
    }

    /**
     * GET /audit-logs/{id}
     * Show detail
     */
    public function show(int $id)
    {
        $log = $this->auditLogRepository->find($id);

        if (!$log) {
           return ApiResponse::notFound('Audit log not found');
        }

        return ApiResponse::success('Audit log retrieved', $log);
    }
}
