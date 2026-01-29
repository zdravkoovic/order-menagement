<?php

namespace App\Application\Order\Commands\CreateOrder;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Application\Errors\InvalidRequestException;
use App\Application\Gateways\CustomerGateway;
use App\Application\Order\ExpirationPolicy\GuestOrderExpirationPolicy;
use App\Application\Order\ExpirationPolicy\RegisteredOrderExpirationPolicy;
use App\Domain\IAggregateRoot;
use App\Domain\Interfaces\IOrderRepository;
use App\Domain\OrderAggregate\CustomerId;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderBuilder;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\Shared\Uuid;
use Symfony\Component\Clock\Clock;

final class CreateOrderCommandHandler extends BaseCommandHandler
{
    private ?Order $createdOrder;
    private ?OrderId $orderId;

    public function __construct(
        private IOrderRepository $orderRepository,
        private GuestOrderExpirationPolicy $guestOrderExpirationPolicy,
        private RegisteredOrderExpirationPolicy $registeredOrderExpirationPolicy,
        private Clock $clock,
        private CustomerGateway $gateway
    ){
        parent::__construct();
    }

    protected function Execute(ICommand $command): Uuid | null
    {
        /** @var CreateOrderCommand $command */

        if(!Uuid::isValid(Uuid::fromString($command->customerId))) throw new InvalidRequestException("Customer ID is invalid", 422);
        if(!$this->gateway->exists($command->customerId)) throw new InvalidRequestException('Customer not found. Register first.', 422);

        $policy = $command->isGuest
            ? $this->guestOrderExpirationPolicy
            : $this->registeredOrderExpirationPolicy;

        $this->createdOrder = OrderBuilder::draft()
            ->forCustomer(CustomerId::fromString($command->customerId))
            ->withExpirationTime($policy->expiresAt($this->clock->now()))
            ->build();
        $this->orderId = $this->orderRepository->save($this->createdOrder);
        return $this->orderId->getId();
    }

    protected function GetAggregateRoot(): IAggregateRoot | null
    {
        return $this->createdOrder ?? null;
    }

    protected function ClearAggregateState(): void
    {
        $this->createdOrder = null;
        $this->orderId = null;
    }
}