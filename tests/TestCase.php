<?php

namespace Tests;

use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\Orderline;
use App\Domain\OrderAggregate\OrderlineBuilder;
use App\Domain\OrderAggregate\ValueObjects\Customer;
use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\ProductId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\EventSourcing\AggregateRoots\FakeAggregateRoot;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected const ORDER_UUID = 'order-uuid';
    protected const CUSTOMER_UUID = 'customer-uuid';
    protected const PRODUCT_ID = 1;
    protected const QUANTITY = 2;
    protected const PRICE = 1600;
    protected const PRODUCT_NAME = 'Bosch wash machine';

    protected Customer $customer;
    protected DateTimeImmutable $expiresAt;

    protected FakeAggregateRoot $order;
    /** @var Orderline[] $orderlines */
    protected array $orderlines;

    /** @var Orderline[] $orderlines */
    protected array $orderlineWithNegativeQuantity;

    protected function setUp(): void
    {
        $this->order = Order::fake(self::ORDER_UUID);

        $this->orderlines = $this->createOrderlines();

        $this->customer = Customer::fromString(self::CUSTOMER_UUID);
        $this->expiresAt = new DateTimeImmutable('+30 minutes');
    }

    /** @return Orderline[] */
    public function createOrderlines(): array
    {
        $product1 = OrderlineBuilder::builder()
            ->withOrder(OrderId::fromString(self::ORDER_UUID))
            ->withPrice(Money::fromFloat(self::PRICE))
            ->withName(self::PRODUCT_NAME)
            ->withProduct(ProductId::fromInt(self::PRODUCT_ID))
            ->withQuantity(Quantity::fromInt(self::QUANTITY))
            ->build();

        return [$product1];
    }

    protected function assertExceptionThrown(callable $callable, string $expectedExceptionClass): void
    {
        try {
            $callable();

            $this->assertTrue(false, "Expected exception `{$expectedExceptionClass}` was not thrown.");
        } catch (Throwable $exception) {
            if (! $exception instanceof $expectedExceptionClass) {
                throw $exception;
            }
            $this->assertInstanceOf($expectedExceptionClass, $exception);
        }
    }

    public function createOrderOnly(): Order
    {
        $uuid = Uuid::generate()->__toString();
        return Order::retrieve($uuid)
            ->createOrder($this->customer, $this->expiresAt)
            ->persist();
    }

    public function createOrderWithItems(): Order
    {
        $uuid = Uuid::generate()->__toString();
        return Order::retrieve($uuid)
            ->createOrder($this->customer, $this->expiresAt, null, $this->orderlines)
            ->persist();
    }
}
