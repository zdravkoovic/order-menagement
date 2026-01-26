<?php

namespace App\Application\Order\Commands\ExpireDraftOrder;

use App\Application\Abstraction\ICommand;
use App\Domain\Shared\Uuid;

final class ExpireDraftOrderCommand implements ICommand
{
    private Uuid $commandId;
    public function __construct() {
        $this->commandId = Uuid::generate();
    }
    public function commandId(): string
    {
        return $this->commandId->__toString();
    }

    public function toLogContext(): array
    {
        return [ 'commandId' => $this->commandId() ];
    }
}