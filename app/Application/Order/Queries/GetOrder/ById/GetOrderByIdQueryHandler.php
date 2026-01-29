<?php

namespace App\Application\Order\Queries\GetOrder\ById;

use App\Application\Abstraction\BaseQueryHandler;
use App\Application\Abstraction\IQuery;
use App\Application\Abstraction\Dto;
use App\Application\Order\DTOs\OrderDto;
use App\Domain\Interfaces\IOrderRepository;
use App\Domain\OrderAggregate\OrderId;

final class GetOrderByIdQueryHandler extends BaseQueryHandler
{
    public function __construct(
        private IOrderRepository $orders
    ) {
        parent::__construct();
    }

    public function execute(IQuery $query): ?Dto
    {
        /** @var GetOrderByIdQuery $query */
        $order = $this->orders->getById(OrderId::fromString($query->id));
        return new OrderDto(
            $order->reference->value(),
            $order->customerId->value(),
            $order->state->value,
            $order->paymentMethod->value,
            $order->totalAmount->value()
        );
    }
}