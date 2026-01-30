<?php

namespace App\Domain\OrderlineAggregate;

final readonly class OrderlineId
{
    private int $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $id) : self
    {
        return new self($id);
    }

    public function value() : int
    {
        return $this->value;
    }

}
