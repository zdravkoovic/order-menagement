<?php

namespace App\Application\Order\Commands\UpdateOrder\AddItem;

use App\Application\Abstraction\IAction;
use App\Application\Abstraction\ICommand;
use App\Domain\Shared\Uuid;

final class UpdateOrderAddItemCommand implements ICommand, IAction
{
    private string $id;

    public function __construct(
        public string $orderId,
        public array $products
    ) {
        $this->id = Uuid::generate()->__toString();
    }

    public function commandId(): string
    {
        return $this->id;
    }

    public function toLogContext(): array
    {
        return [
            'orderId' => $this->orderId,
            'products' => $this->products
        ];
    }
}