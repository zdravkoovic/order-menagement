<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\Dto;
use App\Application\Abstraction\IAction;
use App\Domain\Shared\Uuid;

final class ResultDataTransformMiddleware implements IMiddleware
{
    public function handle(IAction $action, callable $next): ?array
    {
        $result = $next($action);
        return match (true) {
            $result instanceof Uuid => 
                ["id" => $result],
            $result instanceof Dto => $result->getData(),
            is_array($result) => ["ids" => $result],
            default => null
        };
    }
}