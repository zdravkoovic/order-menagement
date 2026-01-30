<?php

namespace App\Domain\OrderAggregate;

use App\Domain\Shared\Uuid;

final readonly class CustomerId
{
    private Uuid $id;

    private function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id) : self
    {
        return new self(Uuid::fromString($id));
    }

    public function value() : string
    {
        return $this->id->__toString();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }
}
