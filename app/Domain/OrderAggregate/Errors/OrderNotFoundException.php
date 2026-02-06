<?php

namespace App\Domain\OrderAggregate\Errors;

use DomainException;

final class OrderNotFoundException extends DomainException
{
    public function __construct(private string $orderId, string $message = 'Requested order not found.') {
        parent::__construct($message);
    }
}