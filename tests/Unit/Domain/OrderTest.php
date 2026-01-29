<?php

namespace Tests\Unit\Domain;

use App\Domain\OrderAggregate\CustomerId;
use App\Domain\OrderAggregate\Errors\PaymentMethodUndefinedException;
use App\Domain\OrderAggregate\Errors\ReferenceUndefinedException;
use App\Domain\OrderAggregate\Errors\TotalAmountViolationException;
use App\Domain\OrderAggregate\Money;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\OrderAggregate\OrderNumber;
use App\Domain\OrderAggregate\OrderState;
use App\Domain\OrderAggregate\PaymentMethod;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function test_pending_order_requires_reference(): void
    {
        $this->expectException(ReferenceUndefinedException::class);

        new Order(
            customerId: CustomerId::fromString(Uuid::generate()->__toString()),
            state: OrderState::PENDING,
            expiresAt: new DateTimeImmutable('-20 hours'),
            paymentMethod: PaymentMethod::MASTER_CARD,
            totalAmount: Money::fromFloat(100),
            orderNumber: null, 
            id: OrderId::fromString(Uuid::generate()->__toString()),
            createdDate: new DateTimeImmutable('-1 day'), 
            lastModifiedDate: null
        );
    }

    public function test_pending_order_requires_payment_method(): void
    {
        $this->expectException(PaymentMethodUndefinedException::class);

        new Order(
            customerId: CustomerId::fromString(Uuid::generate()->__toString()),
            state: OrderState::PENDING,
            expiresAt: new DateTimeImmutable('-20 hours'),
            paymentMethod: PaymentMethod::UNDEFINED,
            totalAmount: Money::fromFloat(100),
            orderNumber: OrderNumber::generate(), 
            id: OrderId::fromString(Uuid::generate()->__toString()),
            createdDate: new DateTimeImmutable('-1 day'), 
            lastModifiedDate: null
        );
    }

    public function test_pending_order_requires_totalAmount(): void
    {
        $this->expectException(TotalAmountViolationException::class);

        new Order(
            customerId: CustomerId::fromString(Uuid::generate()->__toString()),
            state: OrderState::PENDING,
            expiresAt: new DateTimeImmutable('-20 hours'),
            paymentMethod: PaymentMethod::MASTER_CARD,
            totalAmount: Money::zero(),
            orderNumber: OrderNumber::generate(), 
            id: OrderId::fromString(Uuid::generate()->__toString()),
            createdDate: new DateTimeImmutable('-1 day'), 
            lastModifiedDate: null
        );
    }

    public function test_draft_order_expires_when_due(): void
    {
        $order = new Order(
            customerId: CustomerId::fromString(Uuid::generate()->__toString()),
            state: OrderState::DRAFT,
            expiresAt: new DateTimeImmutable('-1 hour')
        );

        $order->isExpired(new DateTimeImmutable());

        $this->assertEquals(OrderState::EXPIRED, $order->state);
    }

    public function test_draft_order_not_expired_before_due(): void
    {
        $order = new Order(
            customerId: CustomerId::fromString(Uuid::generate()->__toString()),
            state: OrderState::DRAFT,
            expiresAt: new DateTimeImmutable('+2 hours')
        );

        $order->isExpired(new DateTimeImmutable());

        $this->assertEquals(OrderState::DRAFT, $order->state);
    }
}