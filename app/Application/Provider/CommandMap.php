<?php

namespace App\Application\Provider;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Application\Order\Commands\CreateOrder\CreateOrderCommandHandler;
use App\Application\Order\Commands\DeleteOrder\DeleteOrderCommand;
use App\Application\Order\Commands\DeleteOrder\DeleteOrderCommandHandler;
use App\Application\Order\Commands\ExpireDraftOrder\ExpireDraftOrderCommand;
use App\Application\Order\Commands\ExpireDraftOrder\ExpireDraftOrderCommandHandler;
use App\Application\Order\Commands\UpdateOrder\AddItem\UpdateOrderAddItemCommand;
use App\Application\Order\Commands\UpdateOrder\AddItem\UpdateOrderAddItemCommandHandler;
use App\Application\Order\Commands\UpdateOrder\RemoveItem\UpdateOrderRemoveItemCommand;
use App\Application\Order\Commands\UpdateOrder\RemoveItem\UpdateOrderRemoveItemCommandHandler;
use App\Application\Orderline\Commands\CreateOrderline\CreateOrderlineCommand;

final class CommandMap
{
    public const MAP = [
        CreateOrderCommand::class => CreateOrderCommandHandler::class,
        ExpireDraftOrderCommand::class => ExpireDraftOrderCommandHandler::class,
        DeleteOrderCommand::class => DeleteOrderCommandHandler::class,
        UpdateOrderAddItemCommand::class => UpdateOrderAddItemCommandHandler::class,
        UpdateOrderRemoveItemCommand::class => UpdateOrderRemoveItemCommandHandler::class
    ];
}