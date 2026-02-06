<?php

namespace App\Application\Errors;

use Throwable;

final class ServiceNotReachedException extends ApplicationException
{
    public function __construct(private array $products, string $message = 'External service failure, try later.', ?Throwable $previous = null) {
        parent::__construct($message);
    }
}