<?php

namespace App\Domain\OrderAggregate\Errors;

use App\Domain\OrderAggregate\OrderId;

final class PaymentMethodUndefinedException extends \DomainException
{
    public function __construct(private readonly OrderId $orderId) {
        $message = "Payment method is required to complete your order.";
        parent::__construct($message);
    }

    public function getOrderId() : OrderId
    {
        return $this->orderId;
    }
}