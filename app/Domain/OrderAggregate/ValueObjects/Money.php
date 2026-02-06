<?php

namespace App\Domain\OrderAggregate\ValueObjects;

use DomainException;

final class Money
{
    private float $value;

    private function __construct(float $value)
    {
        if ($value < 0) throw new DomainException('Money cannot be negative');

        $this->value = $value;
    }

    public static function fromFloat(float $value) : self
    {
        return new self($value);
    }

    public function value() : float
    {
        return $this->value;
    }

    public static function zero() : self
    {
        return new self(0.0);
    }

    public function equals(Money $other): bool {
        return $this->value === $other->value();
    }

    public function multiply(Quantity $quantity): Money
    {
        $this->value *= $quantity->value();
        return $this;
    }

    public function add(Money $money): Money
    {
        $result = $this->value *= $money->value();
        return Money::fromFloat($result);
    }
}