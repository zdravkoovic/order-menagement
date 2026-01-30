<?php

namespace App\Domain\OrderlineAggregate\Events;

use App\Domain\Events\DomainEvent;
use App\Domain\Shared\Uuid;
use Carbon\Carbon;
use DateTime;

class BaseOrderlineDomainEvent extends DomainEvent
{
    public const AGGREGATE_TYPE = 'orderline';
    public function __construct(int $aggregateId, ?DateTime $occuredOnUtc = null)
    {
        // Uuid::generate() is bad choise but let it here for now
        return parent::__construct(Uuid::generate(), $occuredOnUtc ?? Carbon::now(), self::AGGREGATE_TYPE);
    }
}