<?php

namespace App\Application\Errors;


final class InvalidRequestException extends ApplicationException
{
    public function __construct(string $message = '', int $httpStatus = 400)
    {
        return parent::__construct($message, $httpStatus);
    }
}