<?php

namespace App\Application\Order\DTOs;

use App\Application\Abstraction\Dto;

final class OrderDto implements Dto
{
    public function __construct(
        private ?string $reference,
        private string $customerId,
        private string $state,
        private string $paymentMethod,
        private float $totalAmount
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