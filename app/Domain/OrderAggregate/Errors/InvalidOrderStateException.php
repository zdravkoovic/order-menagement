<?php

namespace App\Domain\OrderAggregate\Errors;

use DomainException;

final class InvalidOrderStateException extends DomainException
{
    public function __construct(private string $orderId, private string $state, string $message = '') {
        $message = 'Order cannot be modify if it is not in draft state.';
        parent::__construct($message);
    }
}