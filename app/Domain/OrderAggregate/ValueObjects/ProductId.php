<?php 

namespace App\Domain\OrderAggregate\ValueObjects;

final class ProductId
{
    private int $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromInt(int $id) : self
    {
        return new self($id);
    }

    public function value() : int
    {
        return $this->id;
    }
}