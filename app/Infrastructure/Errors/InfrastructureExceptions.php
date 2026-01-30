<?php

namespace App\Infrastructure\Errors;

use RuntimeException;

class InfrastructureExceptions extends RuntimeException
{
    public function __construct(
        string $message = 'Infrastrucutral excpetion.',
        public int $httpStatus = 400,
        int $code = 0,
        public array $details = []
    ) {
        parent::__construct($message, $code);
    }
}