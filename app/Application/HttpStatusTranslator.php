<?php

namespace App\Application;

use App\Application\Abstraction\ICommand;
use App\Application\Abstraction\IQuery;
use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Application\Order\Commands\DeleteOrder\DeleteOrderCommand;

final class HttpStatusTranslator
{
    public static function command(ICommand $action): int
    {
        return match (true) {
            $action instanceof CreateOrderCommand => 201,
            $action instanceof DeleteOrderCommand => 204,
            default => 200
        };
    }

    public static function query(IQuery $action): int
    {
        return 200;
    }
}
