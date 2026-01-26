<?php

namespace App\Application\Order\Queries\GetOrder\ById;

use App\Application\Abstraction\IAction;
use App\Application\Abstraction\IQuery;
use App\Domain\Shared\Uuid;

final class GetOrderByIdQuery implements IQuery, IAction
{
    private readonly string $queryId;
    public readonly string $id;

    public function __construct(
        string $id
    ) {
        $this->id = $id;
        $this->queryId = Uuid::generate()->__toString();
    }
    public function queryId(): string
    {
        return $this->queryId;
    }

    public function toLogContext(): array
    {
        return [
            'query_id' => $this->queryId,
            'order_id' => $this->id,
        ];
    }
}