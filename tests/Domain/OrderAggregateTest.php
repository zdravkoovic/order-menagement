<?php

namespace Tests\Domain;

use App\Application\Errors\ProductQuantityTooLowException;
use App\Domain\OrderAggregate\Events\OrderCreated;
use App\Domain\OrderAggregate\Events\OrderItemAdded;
use App\Domain\OrderAggregate\Events\OrderItemRemoved;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderlineBuilder;
use App\Domain\OrderAggregate\ValueObjects\Customer;
use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\OrderState;
use App\Domain\OrderAggregate\ValueObjects\PaymentMethod;
use App\Domain\OrderAggregate\ValueObjects\ProductId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAggregateTest extends TestCase
{
    protected Customer $customer;
    protected DateTimeImmutable $expiresAt;

    #[Test]
    public function can_create_order_without_orderlines(): void
    {
        $this->order
            ->given([])
            ->when(function (Order $orderAggregate) : void {
                $orderAggregate->createOrder($this->customer, $this->expiresAt);
            })
            ->assertRecorded([
                new OrderCreated($this->customer->getId()->value(), $this->expiresAt->format('Y-m-d h:i:s'), PaymentMethod::UNDEFINED->value, OrderState::DRAFT->value)
            ]);

    }

    #[Test]
    public function can_create_order_with_orderlines(): void
    {
        $this->order
            ->given([])
            ->when(function (Order $orderAggregate): void {
                $orderAggregate->createOrder($this->customer, $this->expiresAt, null, $this->orderlines);
            })
            ->assertRecorded([
                new OrderCreated($this->customer->getId()->value(), $this->expiresAt->format('Y-m-d h:i:s'), PaymentMethod::UNDEFINED->value, OrderState::DRAFT->value),
                new OrderItemAdded(self::ORDER_UUID, self::PRODUCT_ID, self::QUANTITY, self::PRICE, self::PRODUCT_NAME)
            ]);

    }

    #[Test]
    public function can_add_order_item(): void
    {
        $this->order
            ->given([
                new OrderCreated(self::CUSTOMER_UUID, $this->expiresAt->format('Y-m-d h:i:s')),
                new OrderItemAdded(self::ORDER_UUID, self::PRODUCT_ID, self::QUANTITY, self::PRICE, self::PRODUCT_NAME)
            ])
            ->when(function (Order $orderAggregate): void {
                $orderAggregate->addOrderItems($this->orderlines);
            })
            ->assertRecorded([
                new OrderItemAdded(self::ORDER_UUID, self::PRODUCT_ID, self::QUANTITY, self::PRICE, self::PRODUCT_NAME)
            ]);
    }


    #[Test]
    public function can_remove_order_item(): void
    {
        $this->order
            ->given([
                new OrderCreated(self::CUSTOMER_UUID, $this->expiresAt->format('Y-m-d h:i:s')),
                new OrderItemAdded(self::ORDER_UUID, self::PRODUCT_ID, self::QUANTITY, self::PRICE, self::PRODUCT_NAME)
            ])
            ->when(function (Order $orderAggregate): void {
                $orderAggregate->removeOrderItems($this->orderlines, $this->expiresAt);
            })
            ->assertRecorded([
                new OrderItemRemoved(self::ORDER_UUID, self::PRODUCT_ID, self::QUANTITY, $this->expiresAt->format('Y-m-d h:i:s'))
            ]);
    }


    protected function setUp(): void
    {
        $this->customer = Customer::fromString(self::CUSTOMER_UUID);
        $this->expiresAt = new DateTimeImmutable('+30 minutes');
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
