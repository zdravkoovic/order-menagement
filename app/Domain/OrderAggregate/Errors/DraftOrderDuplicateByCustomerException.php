<?php

namespace App\Domain\OrderAggregate\Errors;

use DomainException;

final class DraftOrderDuplicateByCustomerException extends DomainException
{
    public function __construct(
        public string $order_id, 
        public string $customer_id, 
        string $message = '', 
    ) {
        $message = 'This customer already has drafted order.';
        parent::__construct($message);
    }
}