<?php

namespace App\Domain\OrderlineAggregate;

use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\OrderlineId;
use App\Domain\OrderAggregate\ValueObjects\ProductId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;

class Orderline
{
    private readonly ProductId $productId;
    private readonly Quantity $quantity;
    private readonly Money $price;
    private readonly OrderId $orderId;
    private readonly string $name;

    private function __construct(OrderId $orderId, ProductId $productId, Quantity $quantity, Money $price, string $name)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->orderId = $orderId;
        $this->name = $name;
    }

    public static function create(OrderId $orderId, ProductId $productId, Quantity $quantity, ?Money $price = null, ?string $name = null) : self
    {
        return new self($orderId, $productId, $quantity, $price ?? Money::zero(), $name ?? '');
    }

    public static function reconstitute(
        ProductId $productId,
        Quantity $quantity,
        Money $price,
        OrderId $orderId
    ) : self {
        return new self($orderId, $productId, $quantity, $price, '');
    }

    public function UpdateQuantity(int $value) : self
    {
        $newQuantity = Quantity::fromInt($value);
        $this->quantity = $newQuantity;

        return $this;
    }

    public function getOrderId() : OrderId
    {
        return $this->orderId;
    }
    public function getProductId() : ProductId
    {
        return $this->productId;
    }
    public function getQuantity() : Quantity
    {
        return $this->quantity;
    }
    public function getPrice() : Money
    {
        return $this->price;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getSubTotal() : Money
    {
        return $this->price->multiply($this->quantity);
    }
}

final class OrderlineBuilder
{
    private ?ProductId $productId;
    private ?Quantity $quantity;
    private ?Money $price;
    private ?OrderId $orderId;

    public function __construct()
    {}

    public static function builder() : self
    {
        return new self();
    }

    public function withId(OrderlineId $id) : self
    {
        $this->id = $id;
        return $this;
    }

    public function withProduct(ProductId $productId) : self
    {
        $this->productId = $productId;
        return $this;
    }

    public function withQuantity(Quantity $quantity) : self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function withPrice(Money $price) : self
    {
        $this->price = $price;
        return $this;
    }

    public function withOrder(OrderId $orderId) : self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function build() : Orderline
    {
        return new Orderline($this->orderId, $this->productId, $this->quantity, $this->price);
    }
}