<?php

namespace App\Application\Order\Commands\UpdateOrder\RemoveItem;

use App\Application\Abstraction\IAction;
use App\Application\Abstraction\ICommand;
use App\Domain\Shared\Uuid;

final class UpdateOrderRemoveItemCommand implements ICommand, IAction
{
    private string $commandId;

    public function __construct(
        public string $orderId,
        public array $products 
    ) {
        $this->commandId = Uuid::generate()->__toString();
    }

    public function commandId(): string
    {
        return $this->commandId;
    }

    public function toLogContext(): array
    {
        return [
            'order_id' => $this->orderId,
            'products' => $this->products
        ];
    }
}