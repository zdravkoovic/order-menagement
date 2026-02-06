<?php

namespace App\Infrastructure\Services;

use App\Domain\OrderAggregate\Money;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\OrderlineAggregate\Orderline;
use App\Domain\OrderlineAggregate\OrderlineId;
use App\Domain\OrderlineAggregate\ProductId;
use App\Domain\OrderlineAggregate\Quantity;
use App\Infrastructure\Persistance\Models\OrderlineEntity;

class OrderlineMapper
{
    private function __construct(){}

    // public static function toDomain(OrderlineEntity $data): Orderline
    // {
    //     return Orderline::reconstitute(
    //         OrderlineId::fromInt($data->id),
    //         ProductId::fromString($data->product_id),
    //         Quantity::fromInt($data->quantity),
    //         Money::fromFloat($data->amount),
    //         OrderId::fromString($data->order_id)
    //     );
    // }

    // public static function toEntity(Orderline $orderline): OrderlineEntity
    // {
    //     $entity = new OrderlineEntity();
    //     $entity->id = $orderline->id?->value();
    //     $entity->product_id = $orderline->productId->value();
    //     $entity->quantity = $orderline->quantity->value();
    //     $entity->order_id = $orderline->orderId->value();
    //     return $entity;
    // }
}