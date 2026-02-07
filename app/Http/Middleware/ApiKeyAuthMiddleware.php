<?php

namespace App\Http\Middleware;

use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Support\Config;

class ApiKeyAuthMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $configuredApiKey = (string) Config::get('auth.api_key', '');

        // Optional guard for demo projects: if no API_KEY is configured, auth is bypassed.
        if ($configuredApiKey === '') {
            return $next($request);
        }

        $providedApiKey = (string) ($request->header('x-api-key') ?? '');

        if ($providedApiKey === '' || !hash_equals($configuredApiKey, $providedApiKey)) {
            throw new HttpException(401, 'Unauthorized', ['error' => 'Unauthorized']);
        }

        return $next($request);
    }
}
