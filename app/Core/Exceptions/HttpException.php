<?php

namespace App\Core\Exceptions;

use Exception;

class HttpException extends Exception
{
    public function __construct(
        private readonly int $statusCode,
        string $message,
        private readonly ?array $payload = null
    ) {
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }
}
