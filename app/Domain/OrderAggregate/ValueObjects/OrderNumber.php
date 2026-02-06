<?php

namespace App\Domain\OrderAggregate\ValueObjects;

final class OrderNumber 
{
    private ?string $value;

    private function __construct(?string $value)
    {
        $this->value = $value;
    }

    public static function fromString(?string $reference) : ?self
    {
        if($reference || $reference === '') {
            return null;
        }
        return new self($reference);
    }

    public function value() : ?string
    {
        return $this->value;
    }

    public static function generate() : self
    {
        $uniqueReference = 'ORD-' . strtoupper(bin2hex(random_bytes(5))) . '-' . time();
        return new self($uniqueReference);
    }

}