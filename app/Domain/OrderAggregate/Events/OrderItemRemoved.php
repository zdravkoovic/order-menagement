<?php

namespace App\Domain\OrderAggregate\Events;

use DateTimeImmutable;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

final class OrderItemRemoved extends ShouldBeStored
{
    public function __construct(
        public string $order_id,
        public int $product_id,
        public int $quantity,
        public string $updated_at
    ) {}
}