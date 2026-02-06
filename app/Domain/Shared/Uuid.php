<?php

namespace App\Domain\Shared;

use JsonSerializable;
use Illuminate\Support\Str;

final class Uuid implements JsonSerializable
{
    private ?string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value) : self
    {
        return new self($value);
    }

    public static function generate() : self
    {
        return new self(str::uuid());
    }

    public function equals(self $other) : bool
    {
        return $this->value == $other;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value() : string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public static function isValid(Uuid $uuid) : bool
    {
        return Str::isUuid($uuid->value);
    }
}