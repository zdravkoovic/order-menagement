<?php

namespace App\Application\Abstraction\Bus;

use App\Application\Abstraction\IQuery;
use App\Application\Abstraction\Result;

interface IQueryBus
{
    public function dispatch(IQuery $query) : Result;
}