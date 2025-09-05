<?php

namespace App\Http\Controllers;

use App\Traits\HandlesMedia;

/**
 * @OA\Info(
 *     title="Healthy App",
 *     version="1.0.0",
 *     description="API documentation for Healthy"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    use HandlesMedia;
}
