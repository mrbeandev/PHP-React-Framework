<?php

namespace App\Core\Validation;

use App\Core\Exceptions\HttpException;

class ValidationException extends HttpException
{
    public function __construct(array $errors)
    {
        parent::__construct(422, 'Validation failed.', [
            'error' => 'Validation failed.',
            'errors' => $errors,
        ]);
    }
}
