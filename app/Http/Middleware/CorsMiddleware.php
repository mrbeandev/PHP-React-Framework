<?php

namespace App\Http\Middleware;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Support\Config;

class CorsMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $allowedOrigin = (string) Config::get('cors.allow_origin', '*');
        $allowedMethods = (string) Config::get('cors.allow_methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $allowedHeaders = (string) Config::get('cors.allow_headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key');

        $headers = [
            'Access-Control-Allow-Origin' => $allowedOrigin,
            'Access-Control-Allow-Methods' => $allowedMethods,
            'Access-Control-Allow-Headers' => $allowedHeaders,
        ];

        if ($request->method() === 'OPTIONS') {
            $response = Response::noContent();
            foreach ($headers as $name => $value) {
                $response = $response->withHeader($name, $value);
            }

            return $response;
        }

        $response = $next($request);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
