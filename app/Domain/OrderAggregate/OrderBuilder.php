<?php

namespace App\Domain\OrderAggregate;

use DateTimeImmutable;

use function Laravel\Prompts\info;

final class OrderBuilder
{
    private ?Money $totalAmount = null;
    private ?PaymentMethod $paymentMethod = null;
    private ?CustomerId $customerId = null;
    private ?OrderNumber $reference = null;
    private OrderState $state;
    private ?DateTimeImmutable $expiresAt = null;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?OrderId $id = null;

    private function __construct(){}

    public static function draft() : self
    {
        $b = new self();
        $b->totalAmount = Money::zero();
        $b->paymentMethod = PaymentMethod::UNDEFINED;
        $b->state = OrderState::DRAFT;
        $b->createdAt = new DateTimeImmutable();
        return $b;
    }

    public static function pending() : self
    {
        $b = new self();
        $b->state = OrderState::PENDING;
        $b->updatedAt = new DateTimeImmutable();
        return $b;
    }

    public function withReference(OrderNumber $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function withTotalAmount(Money $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function withPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function forCustomer(CustomerId $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function withExpirationTime(DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function withId(OrderId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withCreationTime(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withLastModifiedTime(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function build(): Order
    {    
        

        return new Order(
            $this->customerId,
            $this->state,
            $this->expiresAt,
            $this->paymentMethod,
            $this->totalAmount,
            $this->reference,
            $this->id,
            $this->createdAt,
            $this->updatedAt
        );
    }
}