<?php

namespace App\Application\Order\Commands\DeleteOrder;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Domain\IAggregateRoot;
use App\Domain\Interfaces\IOrderRepository;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\Shared\Uuid;

final class DeleteOrderCommandHandler extends BaseCommandHandler
{
    private ?Order $deletedOrder;

    public function __construct(private IOrderRepository $orders) 
    {
        parent::__construct();
    }

    protected function Execute(ICommand $command): Uuid|array|null
    {
        /** @var DeleteOrderCommand $command */

        $this->deletedOrder = $this->orders->getById(OrderId::fromString($command->orderId));
        $this->orders->delete(OrderId::fromString($command->orderId));

        return null;
    }

    protected function GetAggregateRoot(): ?IAggregateRoot
    {
        return $this->deletedOrder;
    }

    protected function ClearAggregateState(): void
    {
        $this->deletedOrder = null;
    }
}