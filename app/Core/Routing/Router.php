<?php

namespace App\Core\Routing;

use App\Core\Container\Container;
use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use RuntimeException;

class Router
{
    private array $routes = [];
    private ?array $fallback = null;
    private string $currentPrefix = '';
    private array $currentMiddleware = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function get(string $path, callable|array|string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array|string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array|string $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array|string $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function patch(string $path, callable|array|string $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function options(string $path, callable|array|string $handler): void
    {
        $this->add('OPTIONS', $path, $handler);
    }

    public function fallback(callable|array|string $handler): void
    {
        $this->fallback = [
            'handler' => $handler,
            'middleware' => $this->currentMiddleware,
        ];
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->currentPrefix;
        $previousMiddleware = $this->currentMiddleware;

        $this->currentPrefix = $this->normalizeGroupPrefix($previousPrefix . '/' . trim($prefix, '/'));
        $this->currentMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    public function dispatch(Request $request): Response
    {
        $path = $request->path();
        $method = $request->method();
        $allowedMethods = [];

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            if ($route['method'] !== $method) {
                $allowedMethods[] = $route['method'];
                continue;
            }

            $params = [];
            foreach ($route['params'] as $name) {
                if (isset($matches[$name])) {
                    $params[$name] = urldecode($matches[$name]);
                }
            }

            return $this->runMiddlewarePipeline(
                $route['middleware'],
                $request,
                fn (Request $nextRequest) => $this->invokeHandler($route['handler'], $nextRequest, $params)
            );
        }

        if (!empty($allowedMethods)) {
            $allowHeader = implode(', ', array_unique($allowedMethods));

            if ($method === 'OPTIONS') {
                return Response::noContent()
                    ->withHeader('Allow', $allowHeader);
            }

            throw new HttpException(405, 'Method Not Allowed', [
                'error' => 'Method Not Allowed',
                'allowed_methods' => explode(', ', $allowHeader),
            ]);
        }

        if ($this->fallback !== null) {
            return $this->runMiddlewarePipeline(
                $this->fallback['middleware'],
                $request,
                fn (Request $nextRequest) => $this->invokeHandler($this->fallback['handler'], $nextRequest, [])
            );
        }

        throw new HttpException(404, 'Not Found', ['error' => 'Not Found']);
    }

    private function add(string $method, string $path, callable|array|string $handler): void
    {
        $fullPath = $this->normalizePath($this->currentPrefix . '/' . trim($path, '/'));
        [$pattern, $params] = $this->compilePath($fullPath);

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $fullPath,
            'pattern' => $pattern,
            'params' => $params,
            'handler' => $handler,
            'middleware' => $this->currentMiddleware,
        ];
    }

    private function compilePath(string $path): array
    {
        $normalizedPath = $this->normalizePath($path);
        $params = [];

        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($matches) use (&$params) {
            $params[] = $matches[1];
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $normalizedPath);

        if ($regex === null) {
            throw new RuntimeException('Failed to compile route path.');
        }

        return ['#^' . $regex . '$#', $params];
    }

    private function normalizePath(string $path): string
    {
        $normalized = '/' . ltrim($path, '/');
        $normalized = rtrim($normalized, '/');

        return $normalized === '' ? '/' : $normalized;
    }

    private function invokeHandler(callable|array|string $handler, Request $request, array $params): Response
    {
        $payload = array_merge(['request' => $request], $params);
        $response = $this->container->call($handler, $payload);

        if (!$response instanceof Response) {
            throw new RuntimeException('Route handlers must return an instance of Response.');
        }

        return $response;
    }

    private function runMiddlewarePipeline(array $middleware, Request $request, callable $destination): Response
    {
        $next = $destination;

        foreach (array_reverse($middleware) as $middlewareClass) {
            $next = function (Request $incomingRequest) use ($middlewareClass, $next): Response {
                $middlewareInstance = $this->container->make($middlewareClass);

                if (!method_exists($middlewareInstance, 'handle')) {
                    throw new RuntimeException("Middleware {$middlewareClass} must define handle().");
                }

                $response = $middlewareInstance->handle($incomingRequest, $next);
                if (!$response instanceof Response) {
                    throw new RuntimeException("Middleware {$middlewareClass} must return Response.");
                }

                return $response;
            };
        }

        return $next($request);
    }

    private function normalizeGroupPrefix(string $prefix): string
    {
        $normalized = $this->normalizePath($prefix);
        return $normalized === '/' ? '' : $normalized;
    }
}
