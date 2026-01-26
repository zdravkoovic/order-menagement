<?php

namespace App\Application\Errors\Messages;

final class UserErrorMessage
{
    public function __construct(
        public string $message,
        public int $httpStatus,
    ) {
    }
}