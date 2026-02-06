<?php

namespace App\Infrastructure\Interfaces;

use App\Application\Interfaces\IOrderReadRepository;
use App\Domain\OrderAggregate\Order;

final class OrderReadRepository implements IOrderReadRepository
{
    public function findDraftOrderByCustomerId(string $customerId): ?Order
    {
        $orderRow = \DB::table('orders')
            ->where('customer_id', $customerId)
            ->where('state', 'DRAFT')
            ->first();
        if (!$orderRow) {
            return null; // no draft exists
        }
        $orderId = $orderRow->id;
        return Order::retrieve($orderId);
    }
}