<?php

namespace App\Application\Abstraction\Bus;

use App\Application\Abstraction\Dto;
use App\Application\Abstraction\IQuery;

interface IQueryMiddleware
{
    public function handle(IQuery $query, callable $next) : Dto;
}