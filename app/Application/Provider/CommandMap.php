<?php

namespace App\Application\Provider;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Application\Order\Commands\CreateOrder\CreateOrderCommandHandler;
use App\Application\Order\Commands\ExpireDraftOrder\ExpireDraftOrderCommand;
use App\Application\Order\Commands\ExpireDraftOrder\ExpireDraftOrderCommandHandler;

final class CommandMap
{
    public const MAP = [
        CreateOrderCommand::class => CreateOrderCommandHandler::class,
        ExpireDraftOrderCommand::class => ExpireDraftOrderCommandHandler::class
    ];
}