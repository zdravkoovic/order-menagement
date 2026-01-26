<?php

namespace App\Application\Provider;

use App\Application\Order\Queries\GetOrder\ById\GetOrderByIdQuery;
use App\Application\Order\Queries\GetOrder\ById\GetOrderByIdQueryHandler;

final class QueryMap
{
    public const MAP = [
        GetOrderByIdQuery::class => GetOrderByIdQueryHandler::class
    ];
}