<?php

namespace App\Domain\OrderAggregate;

use App\Application\Errors\ProductNotFoundExcpetion;
use App\Application\Errors\ProductQuantityTooLowException;
use App\Domain\OrderAggregate\Errors\DraftOrderDuplicateByCustomerException;
use App\Domain\OrderAggregate\Errors\InvalidOrderStateException;
use App\Domain\OrderAggregate\Errors\OrderItemMissmatchException;
use App\Domain\OrderAggregate\Errors\OrderNotFoundException;
use App\Domain\OrderAggregate\Errors\OutOfStockQuantityException;
use App\Domain\OrderAggregate\Events\OrderCancelled;
use App\Domain\OrderAggregate\ValueObjects\Customer;
use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\OrderNumber;
use App\Domain\OrderAggregate\ValueObjects\OrderState;
use App\Domain\OrderAggregate\ValueObjects\PaymentMethod;
use App\Domain\OrderlineAggregate\Orderline;
use App\Domain\OrderAggregate\Events\OrderCreated;
use App\Domain\OrderAggregate\Events\OrderItemAdded;
use App\Domain\OrderAggregate\Events\OrderItemRemoved;
use DateTimeImmutable;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class Order extends AggregateRoot
{
    private ?OrderId $id;
    private ?OrderNumber $reference;
    private ?PaymentMethod $paymentMethod;
    private Customer $customer;
    private DateTimeImmutable $expiresAt;
    private DateTimeImmutable $createdDate;
    private ?DateTimeImmutable $lastModifiedDate;
    private OrderState $state;
    /** 
     * @var array<int, array{order_id: string, quantity:int, price:int, order_name:string}> $orderItems 
     * productId => { quantity, price }
    */
    private array $orderItems = [];

    /** @param Orderline[] $orderItems */
    public function createOrder(
        Customer $customer,
        DateTimeImmutable $expiresAt,
        ?PaymentMethod $paymentMethod = null,
        ?array $orderItems = []
    )
    {
        if(isset($this->customerId) && $this->state === OrderState::DRAFT) throw new DraftOrderDuplicateByCustomerException($this->id->value(), $this->customer->getId()->value());

        $this->recordThat(new OrderCreated(
            $customer->getId()->value(),
            $expiresAt->format('Y-m-d h:i:s'),
            $paymentMethod ?? PaymentMethod::UNDEFINED->value,
        ));
        
        if (!isset($orderItems)) return $this;

        foreach($orderItems as $item){
            $this->recordThat(new OrderItemAdded(
                $item->getOrderId()->value(), 
                $item->getProductId()->value(), 
                $item->getQuantity()->value(), 
                $item->getPrice()->value(),
                $item->getName()
            ));
        }
        
        return $this;
    }

    /** @param Orderline[] $items */
    public function addOrderItems(array $items): self
    {
        $this->isOrderStateValid();
        
        foreach ($items as $item) {

            $this->recordThat(new OrderItemAdded(
                $item->getOrderId()->value(),
                $item->getProductId()->value(),
                $item->getQuantity()->value(),
                $item->getPrice()->value(),
                $item->getName()
            ));
        }

        return $this;
    }

    /** @param Orderline[] $products */
    public function removeOrderItems(array $products, DateTimeImmutable $updatedAt): self
    {
        $this->isOrderStateValid();
        
        foreach($products as $product){
            
            $pid = $product->getProductId()->value();
            $quantity = $product->getQuantity()->value();

            if(!$this->isItemPartOfOrder($pid)) throw new OrderItemMissmatchException("You attempt to remove item which is not part of this order.");
            if(!$this->hasItemValidQuantity($pid, $quantity)) throw new ProductQuantityTooLowException($pid, $product->getName(), $quantity, "Product not found.");

            $this->recordThat(new OrderItemRemoved($this->uuid(), $pid, $quantity, $updatedAt->format('Y-m-d h:i:s')));
        }
        return $this;
    }

    public function discardOrder()
    {
        $this->isOrderStateValid();
        
        $this->recordThat(new OrderCancelled(OrderState::CANCELLED->value));
    }

    public function applyOrderCreated(OrderCreated $event)
    {
        $this->customer = Customer::fromString($event->customer_id);
        $this->expiresAt = new DateTimeImmutable($event->expires_at);
        $this->paymentMethod = PaymentMethod::tryFrom($event->payment_method);
        $this->state = OrderState::tryFrom($event->state);
    }

    public function applyOrderCancelled(OrderCancelled $event)
    {
        $this->state = OrderState::tryFrom($event->state);
    }

    public function applyOrderItemAdded(OrderItemAdded $event)
    {
        $pid = $event->product_id;

        if (!isset($this->orderItems[$pid])) {
            $this->orderItems[$pid] = [
                'quantity' => 0,
                'price' => $event->price
            ];
        }
        $this->orderItems[$pid]['quantity'] += $event->quantity;
    }

    public function applyOrderItemRemoved(OrderItemRemoved $event)
    {
        $pid = $event->product_id;

        if (!isset($this->orderItems[$pid])) {
            $this->orderItems[$pid] = [
                'quantity' => 0,
                'price' => $event->price
            ];
        }
        $this->orderItems[$pid]['quantity'] -= $event->quantity;
    }

    public function isExpired(DateTimeImmutable $now) : void {
        if($this->state === OrderState::DRAFT && $this->expiresAt <= $now)
            $this->state = OrderState::EXPIRED;
    }

    /** 
     * @param Orderline[] $products
     * @param Orderline[] $requested
    */
    public function checkStockQuantity(array &$requested, ?array $products): self
    {
        if(!$products) throw new ProductNotFoundExcpetion([-1]);

        foreach($requested as $item)
        {
            $reservedQuantity = 0;
            $pid = $item->getProductId()->value();
            $quantity = $item->getQuantity()->value();

            if(isset($this->orderItems[$pid])) $reservedQuantity = $this->orderItems[$pid]['quantity'];

            if ($reservedQuantity + $quantity >= $products[$pid]['quantity']) throw new OutOfStockQuantityException($this->customer->getId()->value(), $pid, $products[$pid]['name'], $quantity, $products[$pid]['quantity']);
        }
        
        return $this;
    }

    private function isItemPartOfOrder(int $pid): bool
    {
        return isset($this->orderItems[$pid]);
    }

    private function hasItemValidQuantity(int $pid, int $quantity): bool
    {
        return $this->orderItems[$pid]['quantity'] - $quantity >= 0;
    }

    private function isOrderStateValid(): void
    {
        if(!isset($this->state)) throw new OrderNotFoundException($this->uuid(), 'You must make an order first, then you can order items.');
        if($this->state !== OrderState::DRAFT) throw new InvalidOrderStateException($this->uuid(), $this->state->value);
    }

    public function getId(): ?OrderId
    {
        return $this->id;
    }
    public function getReference() : OrderNumber
    {
        return $this->reference;
    }
    public function getTotalAmount() : Money
    {
        return $this->calculateTotalAmount($this->orderItems);
    }
    public function getPaymentMethod() : PaymentMethod
    {
        return $this->paymentMethod;
    }
    public function getCustomer() : Customer
    {
        return $this->customer;
    }
    public function getExpiresAt() : DateTimeImmutable
    {
        return $this->expiresAt;
    }
    public function getCreatedDate() : DateTimeImmutable
    {
        return $this->createdDate;
    }
    public function getLastModifiedDate() : DateTimeImmutable
    {
        return $this->lastModifiedDate;
    }
    public function getOrderState() : OrderState
    {
        return $this->state;
    }
    /** @return ?Orderline[] */
    public function getOrderItems() : array
    {
        return $this->orderItems;
    }

    /** @param Orderline[] $orderItems */
    private static function calculateTotalAmount(array $orderItems): Money
    {
        return array_reduce($orderItems, function ($init, $item) {
            return $item->getSubTotal()->add($init);
        }, Money::zero());
    }
}

final class OrderBuilder
{
    private ?Money $totalAmount = null;
    private ?PaymentMethod $paymentMethod = null;
    private ?Customer $customerId = null;
    private ?OrderNumber $reference = null;
    private OrderState $state;
    private ?DateTimeImmutable $expiresAt = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?OrderId $id = null;
    /** @var Orderline[] $orderItems */
    private ?array $orderItems;

    private function __construct(){}

    public static function draft() : self
    {
        $b = new self();
        $b->totalAmount = Money::zero();
        $b->paymentMethod = PaymentMethod::UNDEFINED;
        $b->state = OrderState::DRAFT;
        $b->createdAt = new DateTimeImmutable();
        $b->orderItems = [];
        return $b;
    }

    public static function pending() : self
    {
        $b = new self();
        $b->state = OrderState::PENDING;
        $b->updatedAt = new DateTimeImmutable();
        return $b;
    }

    public static function expired() : self
    {
        $b = new self();
        $b->state = OrderState::EXPIRED;
        $b->updatedAt = new DateTimeImmutable();
        return $b;
    }

    public function withReference(?OrderNumber $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function withPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function forCustomer(Customer $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function withExpirationTime(DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function withId(OrderId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withCreationTime(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withLastModifiedTime(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /** @param Orderline[] $orderItems */
    public function withOrderItems(array $orderItems): self
    {
        $this->orderItems = $orderItems;
        return $this;
    }

    public function build(): Order
    {    
        return new Order(
            $this->customerId,
            $this->state,
            $this->expiresAt,
            $this->paymentMethod,
            $this->orderItems,
            $this->reference,
            $this->id,
            $this->createdAt,
            $this->updatedAt
        );
    }
}