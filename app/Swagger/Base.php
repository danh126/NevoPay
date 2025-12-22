<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="NevoPay API",
 *     version="1.0.0",
 *     description="API documentation for NevoPay wallet system"
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Local server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class Base{}
