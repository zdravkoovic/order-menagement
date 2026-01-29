<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\IAction;
use App\Application\Abstraction\ICommand;
use Illuminate\Support\Facades\DB;

final class TransactionMiddleware implements IMiddleware
{
    /** @param ICommand $command */
    public function handle(IAction $action, callable $next): ?array
    {   
        DB::beginTransaction();
        try {
            $result = $next($action);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}