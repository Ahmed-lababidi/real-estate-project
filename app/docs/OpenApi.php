<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     title="Real Estate API",
 *     version="1.0.0",
 *     description="API documentation for Real Estate Mobile App Backend"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi
{
    //
}
