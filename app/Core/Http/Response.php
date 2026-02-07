<?php

namespace App\Core\Http;

use RuntimeException;

class Response
{
    private ?string $filePath = null;

    public function __construct(
        private string $body = '',
        private int $status = 200,
        private array $headers = []
    ) {
    }

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($payload === false) {
            throw new RuntimeException('Failed to encode JSON response.');
        }

        return new self($payload, $status, array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers));
    }

    public static function text(string $text, int $status = 200, array $headers = []): self
    {
        return new self($text, $status, array_merge(['Content-Type' => 'text/plain; charset=utf-8'], $headers));
    }

    public static function html(string $html, int $status = 200, array $headers = []): self
    {
        return new self($html, $status, array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers));
    }

    public static function noContent(int $status = 204, array $headers = []): self
    {
        return new self('', $status, $headers);
    }

    public static function file(string $filePath, ?string $contentType = null): self
    {
        if (!is_file($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        $response = new self('', 200);
        $response->filePath = $filePath;
        $response->headers['Content-Type'] = $contentType ?: self::detectContentType($filePath);
        $response->headers['Content-Length'] = (string) filesize($filePath);

        return $response;
    }

    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    public function statusCode(): int
    {
        return $this->status;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        if ($this->filePath !== null) {
            readfile($this->filePath);
            return;
        }

        echo $this->body;
    }

    private static function detectContentType(string $filePath): string
    {
        $mimes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'html' => 'text/html; charset=utf-8',
        ];

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (isset($mimes[$extension])) {
            return $mimes[$extension];
        }

        $detected = mime_content_type($filePath);
        return $detected ?: 'application/octet-stream';
    }
}
