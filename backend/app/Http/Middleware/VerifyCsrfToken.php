<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '*',               // Exclude all API routes from CSRF verification
        'api/*',               // Exclude all API routes from CSRF verification
        'api/documentation/*', // Adjust according to your Swagger route
        'api/v1/font/upload',  // Ensure your specific API endpoint is excluded
    ];
}
