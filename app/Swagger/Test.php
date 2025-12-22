<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/swagger-test",
 *     summary="Swagger test endpoint",
 *     @OA\Response(response=200, description="OK")
 * )
 */
class Test {}
