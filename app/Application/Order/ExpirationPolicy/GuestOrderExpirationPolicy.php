<?php

namespace App\Application\Order\ExpirationPolicy;

use App\Domain\OrderAggregate\Ports\OrderExpirationPolicy;
use DateInterval;
use DateTimeImmutable;

final class GuestOrderExpirationPolicy implements OrderExpirationPolicy
{
    public function expiresAt(DateTimeImmutable $now): DateTimeImmutable
    {
        return $now->add(new DateInterval('PT30M'));
    }
}