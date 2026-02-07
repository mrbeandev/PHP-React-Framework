<?php

namespace App\Core\Http;

use App\Core\Exceptions\HttpException;

class Request
{
    private ?array $jsonBody = null;

    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $headers,
        private readonly string $rawBody
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = self::normalizePath($path);

        return new self(
            $method,
            $path,
            $_GET,
            self::extractHeaders(),
            file_get_contents('php://input') ?: ''
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $normalized = strtolower($key);
        return $this->headers[$normalized] ?? $default;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }

    public function json(): array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        if ($this->rawBody === '') {
            $this->jsonBody = [];
            return $this->jsonBody;
        }

        $decoded = json_decode($this->rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new HttpException(400, 'Invalid JSON payload.', ['error' => 'Invalid JSON payload.']);
        }

        $this->jsonBody = $decoded;
        return $this->jsonBody;
    }

    public function isApiRequest(): bool
    {
        return str_starts_with($this->path, '/api');
    }

    private static function normalizePath(string $path): string
    {
        $normalized = '/' . ltrim($path, '/');
        $normalized = rtrim($normalized, '/');

        return $normalized === '' ? '/' : $normalized;
    }

    private static function extractHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                $normalized = [];
                foreach ($headers as $key => $value) {
                    $normalized[strtolower($key)] = $value;
                }
                return $normalized;
            }
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            $headerKey = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$headerKey] = $value;
        }

        return $headers;
    }
}
