<?php

namespace App\Application\Order\DTOs;

use App\Application\Abstraction\Dto;

final class OrderDto implements Dto
{
    public function __construct(
        private readonly ?string $reference,
        private readonly string  $customerId,
        private readonly string  $state,
        private readonly string  $paymentMethod,
        private readonly float   $totalAmount
    ) {

    }
    public function getData() : array
    {
        return [
            "reference" => $this->reference,
            "customer_id" => $this->customerId,
            "state" => $this->state,
            "payment_method" => $this->paymentMethod,
            "totalAmount" => $this->totalAmount
        ];
    }
}
