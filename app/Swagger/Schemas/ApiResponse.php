<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 */
class ApiResponse {}