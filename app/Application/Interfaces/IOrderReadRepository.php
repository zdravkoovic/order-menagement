<?php

namespace App\Application\Interfaces;

use App\Domain\OrderAggregate\Order;

interface IOrderReadRepository
{
    /**
     * @param string $customerId
     * @return Order|null Returns the Order aggregate in DRAFT state, or null if none exists
    */
    public function findDraftOrderByCustomerId(string $customerId): ?Order;
}
