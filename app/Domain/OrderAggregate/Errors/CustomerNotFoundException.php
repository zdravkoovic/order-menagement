<?php

namespace App\Domain\OrderAggregate\Errors;

final class CustomerNotFoundException extends \DomainException
{
    protected $message;

    public function __construct(private string $customerId) {
        $this->message = "Cannot find given customer.";
    }
}