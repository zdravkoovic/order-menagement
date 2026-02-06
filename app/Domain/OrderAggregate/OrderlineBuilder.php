<?php

namespace App\Domain\OrderAggregate;

use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\ProductId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;

final class OrderlineBuilder
{
    private ?ProductId $productId;
    private ?Quantity $quantity;
    private ?Money $price;
    private ?OrderId $orderId;
    private ?string $name;

    public function __construct()
    {}

    public static function builder() : self
    {
        return new self();
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

    public function withName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }
    
    public function build() : Orderline
    {
        return Orderline::create($this->orderId, $this->productId, $this->quantity, $this->price, $this->name);
    }
}