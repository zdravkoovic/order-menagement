<?php

namespace App\Domain\OrderAggregate\ValueObjects;

final class ShippingAddress
{
    private readonly string $street;
    private readonly int $houseNumber;
    private readonly string $country;
    private readonly string $zipCode;

    public function __construct(string $street, int $houseNumber, string $country, string $zipCode) {
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->country = $country;
        $this->zipCode = $zipCode;
    }

    public function getStreet(): string
    {
        return $this->street;
    }
    public function getHouseNumber(): int
    {
        return $this->houseNumber;
    }
    public function getCountry(): string
    {
        return $this->country;
    }
    public function getZipCode(): string
    {
        return $this->zipCode;
    }
    public function __toString(): string
    {
        return $this->street . ' ' . $this->houseNumber . ', ' . $this->country . ' ' . $this->zipCode;
    }
}