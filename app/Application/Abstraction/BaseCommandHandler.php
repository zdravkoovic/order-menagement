<?php

namespace App\Application\Abstraction;

use App\Domain\IAggregateRoot;
use App\Domain\Shared\Uuid;

abstract class BaseCommandHandler implements ICommandHandler
{
    public function __construct(
        // private IDomainEventDispatcher $domainEventDispatcher
    ){}

    public function handle(ICommand $command) : Uuid | array | null
    {
        $result = $this->Execute($command);
        
        return $result;
    }

    protected abstract function Execute(ICommand $command) : Uuid | array | null;
    protected abstract function ClearAggregateState() : void;
}