<?php

namespace App\Application\Abstraction;

use App\Application\Errors\Messages\UserErrorMessage;
use App\Domain\Shared\Uuid;

final class Result
{
    private function __construct(
        public bool $success,
        public ?array $data = [],
        public ?int $httpStatus,
        public ?UserErrorMessage $appError = null
    ){} 

    public static function success(array $data, ?int $httpStatus = 200) : self
    {
        return new self(true, $data, $httpStatus);
    }

    public static function fail(UserErrorMessage $appError, int $httpStatus = 400) : self
    {
        return new self(false, null, $appError->httpStatus ?? $httpStatus, $appError);
    }
}