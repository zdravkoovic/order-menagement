<?php

namespace App\Domain\OrderlineAggregate;

use App\Domain\Shared\Uuid;

final class OrderlineId
{
    private readonly int $value;

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