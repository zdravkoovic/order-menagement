<?php

namespace App\Domain\OrderAggregate;

use App\Domain\AggregateRoot;
use App\Domain\IAggregateRoot;
use App\Domain\OrderAggregate\Errors\CustomerNotFoundException;
use App\Domain\OrderAggregate\Errors\OrderExpirationTimeViolated;
use App\Domain\OrderAggregate\Errors\PaymentMethodUndefinedException;
use App\Domain\OrderAggregate\Errors\ReferenceUndefinedException;
use App\Domain\OrderAggregate\Errors\TotalAmountViolationException;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;

class Order implements IAggregateRoot
{
    use AggregateRoot;

    public readonly ?OrderId $id;
    public readonly ?OrderNumber $reference;
    public readonly ?Money $totalAmount;
    public readonly ?PaymentMethod $paymentMethod;
    public readonly OrderState $state;
    public readonly CustomerId $customerId;
    public readonly DateTimeImmutable $expiresAt;
    public readonly DateTimeImmutable $createdDate;
    public readonly ?DateTimeImmutable $lastModifiedDate;

    public function __construct(
        CustomerId $customerId,
        OrderState $state,
        DateTimeImmutable $expiresAt,
        ?PaymentMethod $paymentMethod = null, 
        ?Money $totalAmount = null, 
        ?OrderNumber $orderNumber = null, 
        ?OrderId $id = null, 
        ?DateTimeImmutable $createdDate, 
        ?DateTimeImmutable $lastModifiedDate = null
    )
    {
        // invariants
        if($state === OrderState::PENDING && !$orderNumber) throw new ReferenceUndefinedException($id);
        if($state === OrderState::PENDING && !$totalAmount) throw new TotalAmountViolationException($id);
        if($state === OrderState::PENDING && !$paymentMethod) throw new PaymentMethodUndefinedException($id);

        $this->id = $id;
        $this->reference = $orderNumber;
        $this->totalAmount = $totalAmount;
        $this->paymentMethod = $paymentMethod;
        $this->customerId = $customerId;
        $this->createdDate = $createdDate ?? new DateTimeImmutable();
        $this->lastModifiedDate = $lastModifiedDate;
        $this->state = $state;
        $this->expiresAt = $expiresAt;
    }

    public function isExpired(DateTimeImmutable $now) : void {
        if($this->state === OrderState::DRAFT && $this->expiresAt <= $now)
            $this->state = OrderState::EXPIRED;
    }
}