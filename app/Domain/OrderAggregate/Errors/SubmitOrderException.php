<?php

namespace App\Domain\OrderAggregate\Errors;

use App\Domain\OrderAggregate\ValueObjects\OrderId;
use DomainException;

final class SubmitOrderException extends DomainException
{
    public function __construct(private readonly OrderId $orderId) {
        $message = "You cannot make an order without order lines.";
        parent::__construct($message);
    }

    public function getOrderId() : OrderId
    {
        return $this->orderId;
    }
}