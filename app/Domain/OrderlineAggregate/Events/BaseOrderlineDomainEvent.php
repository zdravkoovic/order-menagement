<?php

namespace App\Domain\OrderlineAggregate\Events;

use App\Domain\Events\DomainEvent;
use Carbon\Carbon;
use DateTime;

class BaseOrderlineDomainEvent extends DomainEvent
{
    public const AGGREGATE_TYPE = 'orderline';
    public function __construct(int $aggregateId, ?DateTime $occuredOnUtc = null)
    {
        return parent::__construct($aggregateId, $occuredOnUtc ?? Carbon::now(), self::AGGREGATE_TYPE);
    }
}