<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\IAction;
use App\Application\Abstraction\ICommand;
use Illuminate\Support\Facades\DB;
use Throwable;

final class TransactionMiddleware implements IMiddleware
{
    public function handle(IAction $action, callable $next): ?array
    {   
        $result = DB::transaction(function () use ($next, $action) {
            return $next($action);
        });
        return $result;
    }
}