<?php

namespace App\Application\Abstraction\Bus;

use App\Application\Abstraction\IAction;

interface IMiddleware
{
    public function handle(IAction $action, callable $next): ?array;
}