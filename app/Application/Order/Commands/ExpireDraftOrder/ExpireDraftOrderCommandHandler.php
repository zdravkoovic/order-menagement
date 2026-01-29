<?php

namespace App\Application\Order\Commands\ExpireDraftOrder;

use App\Application\Abstraction\ICommand;
use App\Application\Abstraction\ICommandHandler;
use App\Domain\Interfaces\IOrderRepository;
use App\Domain\OrderAggregate\Order;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;
use Illuminate\Support\Facades\Log;

final class ExpireDraftOrderCommandHandler implements ICommandHandler
{
    public function __construct(
        private IOrderRepository $orders,
    )
    {}
    public function handle(ICommand $command): ?Uuid
    {
        /** @var ExpireDraftOrderCommand $command */
        $now = new DateTimeImmutable();
        foreach($this->orders->findExpiratedOrderDrafts($now) as $order)
        {
            /** @var Order $order */
            $order->isExpired($now);
            $this->orders->updateStateToExpire($order->id);
        }
        return null;
    }
}