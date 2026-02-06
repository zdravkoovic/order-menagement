<?php

namespace App\Domain\OrderAggregate\Errors;

use DomainException;

final class OutOfStockQuantityException extends DomainException
{
    public function __construct(
        private string $customerId, 
        private int $productId, 
        private string $productName,
        int $requestedQuantity, 
        int $stockQuantity, 
        string $message = ''
    ) {
        $message = 'Out of stock quantity.';
        parent::__construct($message);
    }
}