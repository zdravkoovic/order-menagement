<?php

namespace App\Application\Order\Commands\ExpireDraftOrder;

use App\Application\Abstraction\ICommand;
use App\Application\Abstraction\ICommandHandler;
use App\Domain\Shared\Uuid;

final class ExpireDraftOrderCommandHandler implements ICommandHandler
{
    public function __construct(
    )
    {}
    public function handle(ICommand $command): ?Uuid
    {
        // /** @var ExpireDraftOrderCommand $command */
        // $now = new DateTimeImmutable();
        // foreach($this->orders->findExpiratedOrderDrafts($now) as $order)
        // {
        //     /** @var Order $order */
        //     $order->isExpired($now);
        //     $this->orders->updateStateToExpire($order->id);
        // }
        return null;
    }
}