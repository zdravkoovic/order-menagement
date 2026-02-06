<?php

namespace App\Application\Order\Commands\DeleteOrder;

use App\Application\Abstraction\BaseCommandHandler;
use App\Application\Abstraction\ICommand;
use App\Domain\IAggregateRoot;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\Shared\Uuid;

final class DeleteOrderCommandHandler extends BaseCommandHandler
{
    private ?Order $deletedOrder;

    public function __construct() 
    {
        parent::__construct();
    }

    protected function Execute(ICommand $command): Uuid|array|null
    {
        /** @var DeleteOrderCommand $command */
        return null;
    }


    protected function ClearAggregateState(): void
    {
        $this->deletedOrder = null;
    }
}