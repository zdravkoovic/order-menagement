<?php

namespace App\Domain\OrderAggregate\Errors;

use DomainException;

final class OrderItemMissmatchException extends DomainException
{
    public function __construct(?string $message = '') {
        parent::__construct($message);
    }
}