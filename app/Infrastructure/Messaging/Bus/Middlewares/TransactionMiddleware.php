<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\IAction;
use Illuminate\Support\Facades\DB;

final class TransactionMiddleware implements IMiddleware
{
    public function handle(IAction $action, callable $next): ?array
    {
        DB::transaction(function () use ($next, $action) {
            return $next($action);
        });
        return null;
    }
}
