<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\IAction;
use Illuminate\Support\Facades\DB;

final class TransactionMiddleware implements IMiddleware
{
    /** @param ICommand $command */
    public function handle(IAction $command, callable $next): ?array
    {   
        DB::beginTransaction();
        try {
            $result = $next($command);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}