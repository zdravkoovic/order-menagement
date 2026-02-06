<?php

namespace App\Domain\OrderAggregate\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

final class OrderCancelled extends ShouldBeStored
{
    public function __construct(public string $state) {
    }
}