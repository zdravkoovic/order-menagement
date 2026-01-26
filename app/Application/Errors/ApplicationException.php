<?php

namespace App\Application\Errors;

use RuntimeException;

class ApplicationException extends RuntimeException
{
    public function __construct(
        string $message = '',
        public int $httpStatus = 400,
        int $code = 0,
        ?\Throwable $previous = null
    ){
        parent::__construct($message, $code, $previous);
    }
}