<?php

namespace App\Domain\OrderAggregate\ValueObjects;

use DomainException;

final class Quantity
{
    private int $quantity;

    private function __construct(int $value)
    {
        if($value <= 0) throw new DomainException("Quantity must be greater then zero.");

        $this->quantity = $value;
    }

    public static function fromInt(int $value) : self
    {
        return new self($value);
    }

    public function value() : int
    {
        return $this->quantity;
    }

    public function equals(Quantity $other) : bool
    {
        return $this->quantity == $other->value();
    }

    public function add(Quantity $other) : self
    {
        return new self($this->quantity + $other->value());
    }

    public function substract(Quantity $other) : self
    {
        $new = ($this->quantity - $other->value());

        if($new <= 0) throw new DomainException("Resulting quantity must be greater then zero.");

        return new self($new);
    }

    
}