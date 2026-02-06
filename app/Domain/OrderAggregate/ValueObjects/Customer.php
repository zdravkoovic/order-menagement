<?php

namespace App\Domain\OrderAggregate\ValueObjects;

use App\Domain\Shared\Uuid;

final class Customer
{
    private readonly Uuid $id;
    private readonly ?ShippingAddress $shippingAddress;

    public function __construct(Uuid $id, ?ShippingAddress $shippingAddress = null)
    {
        $this->id = $id;
        $this->shippingAddress = $shippingAddress;
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    public function addShippingAddress(string $street, int $houseNumber, string $country, string $zipCode): self
    {
        $this->shippingAddress = new ShippingAddress($street, $houseNumber, $country, $zipCode);
        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shippingAddress;
    }
}