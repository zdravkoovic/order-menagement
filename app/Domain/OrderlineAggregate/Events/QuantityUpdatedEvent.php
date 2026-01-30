<?php

namespace App\Domain\OrderlineAggregate\Events;

use App\Domain\OrderlineAggregate\Orderline;

final class QuantityUpdatedEvent extends BaseOrderlineDomainEvent
{
    public Orderline $orderline;
    public function __construct(Orderline $orderline)
    {
        $this->orderline = $orderline;
        return parent::__construct($this->orderline->id->value());
    }
}