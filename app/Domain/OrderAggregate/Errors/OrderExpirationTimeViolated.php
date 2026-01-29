<?php

namespace App\Domain\OrderAggregate\Errors;

use DateTimeImmutable;

final class OrderExpirationTimeViolated extends \DomainException
{
    public function __construct(private readonly DateTimeImmutable $creationTime, private readonly DateTimeImmutable $expirationTime) 
    {
        $message = "Expiration time was exceeded. Please, create new order.";
        parent::__construct($message);
    }

    public function getExpirationTime() : DateTimeImmutable
    {
        return $this->expirationTime;
    }

    public function getCretionTime() : DateTimeImmutable
    {
        return $this->creationTime;
    }
}