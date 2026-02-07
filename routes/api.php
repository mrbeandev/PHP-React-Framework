<?php

use App\Controllers\Api\SeoController;
use App\Controllers\Api\SettingController;
use App\Controllers\Api\TodoController;
use App\Core\Routing\Router;
use App\Http\Middleware\ApiKeyAuthMiddleware;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\RequestLoggingMiddleware;

$registerTodoRoutes = function (Router $router, string $prefix = ''): void {
    $router->get("{$prefix}/todos", [TodoController::class, 'index']);
    $router->post("{$prefix}/todos", [TodoController::class, 'store']);
    $router->get("{$prefix}/todos/{id}", [TodoController::class, 'show']);
    $router->put("{$prefix}/todos/{id}", [TodoController::class, 'update']);
    $router->delete("{$prefix}/todos/{id}", [TodoController::class, 'destroy']);
};

$registerSeoRoutes = function (Router $router, string $prefix = ''): void {
    $router->get("{$prefix}/seo", [SeoController::class, 'index']);
    $router->post("{$prefix}/seo", [SeoController::class, 'upsert']);
};

$registerSettingRoutes = function (Router $router, string $prefix = ''): void {
    $router->get("{$prefix}/settings/seo-toggle", [SettingController::class, 'getSeoToggle']);
    $router->post("{$prefix}/settings/seo-toggle", [SettingController::class, 'updateSeoToggle']);
};

$router->group('/api', function (Router $router) use ($registerTodoRoutes, $registerSeoRoutes, $registerSettingRoutes): void {
    $router->group('/v1', function (Router $router) use ($registerTodoRoutes, $registerSeoRoutes, $registerSettingRoutes): void {
        $registerTodoRoutes($router);

        $router->group('', function (Router $router) use ($registerSeoRoutes, $registerSettingRoutes): void {
            $registerSeoRoutes($router);
            $registerSettingRoutes($router);
        }, [ApiKeyAuthMiddleware::class]);
    });
}, [CorsMiddleware::class, RequestLoggingMiddleware::class]);
