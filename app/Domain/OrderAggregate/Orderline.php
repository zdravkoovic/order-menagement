<?php

namespace App\Domain\OrderAggregate;

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