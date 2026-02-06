<?php

namespace App\Infrastructure\Messaging\Bus\Middlewares;

use App\Application\Abstraction\Bus\IMiddleware;
use App\Application\Abstraction\Dto;
use App\Application\Abstraction\IAction;
use App\Domain\Shared\Uuid;

use function Laravel\Prompts\info;

final class ResultDataTransformMiddleware implements IMiddleware
{
    public function handle(IAction $action, callable $next): ?array
    {
        $result = $next($action);
        info("Result: " . get_class($result));
        info("Uuid: " . Uuid::class);
        return match (true) {
            $result instanceof Uuid => 
                ["id" => $result->value()],
            $result instanceof Dto => $result->getData(),
            is_array($result) => ["ids" => $result],
            default => null
        };
    }
}