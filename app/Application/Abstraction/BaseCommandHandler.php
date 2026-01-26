<?php

namespace App\Application\Abstraction;

use App\Domain\IAggregateRoot;
use App\Domain\Shared\Uuid;

abstract class BaseCommandHandler implements ICommandHandler
{
    public function __construct(
        // private IDomainEventDispatcher $domainEventDispatcher
    ){}

    public function handle(ICommand $command) : Uuid | null
    {
        $result = $this->Execute($command);

        if($result) {
            $this->ClearAggregateState();
            return $result;
        }

        $aggregate = $this->GetAggregateRoot();
        if($aggregate != null)
        {
            foreach($aggregate->PopDomainEvents() as $event)
            {
                // $this->domainEventDispatcher->Dispatch($event);
            }
        }

        return $result;
    }

    protected abstract function Execute(ICommand $command) : Uuid | null;
    protected abstract function GetAggregateRoot() : IAggregateRoot | null;
    protected abstract function ClearAggregateState() : void;
}