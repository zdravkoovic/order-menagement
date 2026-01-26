<?php

namespace App\Application\Order\ExpirationPolicy;

use App\Domain\OrderAggregate\Ports\OrderExpirationPolicy;
use DateInterval;
use DateTimeImmutable;

class RegisteredOrderExpirationPolicy implements OrderExpirationPolicy
{
    public function expiresAt(DateTimeImmutable $now): \DateTimeImmutable
    {
        return $now->add(new DateInterval('PT2H'));
    }
}