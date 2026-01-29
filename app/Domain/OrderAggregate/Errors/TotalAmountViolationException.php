<?php

namespace App\Domain\OrderAggregate\Errors;

use App\Domain\OrderAggregate\OrderId;

final class TotalAmountViolationException extends \DomainException
{
    public function __construct(private readonly OrderId $orderId) {
        $message = "Total amount cannot be zero in order to complete your order.";
        parent::__construct($message);
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}