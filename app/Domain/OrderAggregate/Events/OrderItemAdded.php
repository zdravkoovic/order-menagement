<?php

namespace App\Domain\OrderAggregate\Events;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

final class OrderItemAdded extends ShouldBeStored
{
    public function __construct(
        public string $order_id,
        public int $product_id,
        public int $quantity,
        public float $price,
        public string $name
    ) {
    }
}