<?php

namespace App\Domain\OrderAggregate\Ports;

use DateTimeImmutable;

interface OrderExpirationPolicy
{
    public function expiresAt(DateTimeImmutable $now): DateTimeImmutable;
}
