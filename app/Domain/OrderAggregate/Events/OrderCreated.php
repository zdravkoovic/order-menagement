<?php

namespace App\Domain\OrderAggregate\Events;

use App\Domain\OrderAggregate\ValueObjects\Customer;
use App\Domain\OrderAggregate\ValueObjects\OrderState;
use App\Domain\OrderAggregate\ValueObjects\PaymentMethod;
use DateTimeImmutable;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OrderCreated extends ShouldBeStored
{
    public function __construct(
        public string $customer_id,
        public string $expires_at,
        public ?string $payment_method = PaymentMethod::UNDEFINED->value,
        public ?string $state = OrderState::DRAFT->value
    )
    {
    }
}
