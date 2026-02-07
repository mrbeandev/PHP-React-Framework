<?php

namespace App\Http\Middleware;

use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use Throwable;

class RequestLoggingMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            error_log(sprintf(
                '[Request] %s %s %d %dms',
                $request->method(),
                $request->path(),
                $response->statusCode(),
                $durationMs
            ));

            return $response;
        } catch (Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

            error_log(sprintf(
                '[Request] %s %s %d %dms (%s)',
                $request->method(),
                $request->path(),
                $statusCode,
                $durationMs,
                $exception->getMessage()
            ));

            throw $exception;
        }
    }
}
