<?php

namespace App\Domain\OrderAggregate\ValueObjects;

enum OrderState: string
{
    case PENDING = 'PENDING';
    case DRAFT = 'DRAFT';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
    case DELIVERED = 'DELIVERED';
}