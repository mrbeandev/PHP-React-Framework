<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Core\Container\Container;
use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\Support\Config;
use App\Providers\AppServiceProvider;

$container = new Container();
(new AppServiceProvider(__DIR__ . '/dist'))->register($container);

$request = Request::fromGlobals();
$router = new Router($container);

require __DIR__ . '/../routes/api.php';
require __DIR__ . '/../routes/web.php';

try {
    $response = $router->dispatch($request);

    if ($request->isApiRequest()) {
        $response = appendApiCorsHeaders($response);
    }

    $response->send();
} catch (HttpException $exception) {
    $payload = $exception->getPayload() ?? ['error' => $exception->getMessage()];

    if ($exception->getStatusCode() === 405 && isset($payload['allowed_methods']) && is_array($payload['allowed_methods'])) {
        header('Allow: ' . implode(', ', $payload['allowed_methods']));
    }

    if ($request->isApiRequest()) {
        $response = Response::json($payload, $exception->getStatusCode());
        $response = appendApiCorsHeaders($response);
        $response->send();
        exit;
    }

    Response::text($exception->getMessage(), $exception->getStatusCode())->send();
} catch (Throwable $exception) {
    error_log('[TaskFlow Error] ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());

    $message = Config::get('app.env') === 'development'
        ? $exception->getMessage()
        : 'An unexpected error occurred.';

    if ($request->isApiRequest()) {
        $response = Response::json(['error' => 'Internal Server Error', 'message' => $message], 500);
        $response = appendApiCorsHeaders($response);
        $response->send();
        exit;
    }

    Response::text($message, 500)->send();
}

function appendApiCorsHeaders(Response $response): Response
{
    $allowedOrigin = (string) Config::get('cors.allow_origin', '*');
    $allowedMethods = (string) Config::get('cors.allow_methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
    $allowedHeaders = (string) Config::get('cors.allow_headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key');

    return $response
        ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
        ->withHeader('Access-Control-Allow-Methods', $allowedMethods)
        ->withHeader('Access-Control-Allow-Headers', $allowedHeaders);
}
