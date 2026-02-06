<?php

namespace App\Application\Order\Commands;

use App\Domain\OrderAggregate\ValueObjects\Money;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\ProductId;
use App\Domain\OrderAggregate\ValueObjects\Quantity;
use App\Domain\OrderlineAggregate\Orderline;

final class UnpackingOrderItems
{
    /** @return Orderline[] */
    public static function unpackingOrderItems(OrderId $orderId, array $requested, ?array $products): ?array
    {
        if($products === []) return null;

        $orderlines = collect($requested)->map(fn ($value) => Orderline::create(
            $orderId,
            ProductId::fromInt($value['product_id']),
            Quantity::fromInt($value['quantity']),
            Money::fromFloat($products[$value['product_id']]['price']),
            $products[$value['product_id']]['name']
        ));

        return $orderlines->all();
    }
}