<?php

namespace App\Domain\OrderAggregate\Projectors;

use App\Application\Errors\ProductQuantityTooLowException;
use App\Domain\OrderAggregate\Events\OrderCreated;
use App\Domain\OrderAggregate\Events\OrderItemAdded;
use App\Domain\OrderAggregate\Events\OrderItemRemoved;
use App\Infrastructure\Persistance\Models\OrderEntity;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class OrderProjector extends Projector
{
    public function onOrderCreated(OrderCreated $event)
    {
        OrderEntity::create([
            'id' => $event->aggregateRootUuid(),
            'customer_id' => $event->customer_id,
            'expires_at' => $event->expires_at,
            'payment_method' => $event->payment_method
        ]);
    }

    public function onOrderItemAdded(OrderItemAdded $event): void
    {
        DB::statement(
            '
            INSERT INTO orderlines (
                order_id, product_id, order_name, price, quantity, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ON CONFLICT (order_id, product_id)
            DO UPDATE SET
                quantity   = orderlines.quantity + EXCLUDED.quantity,
                price      = EXCLUDED.price,
                order_name = EXCLUDED.order_name,
                updated_at = NOW()
            ',
            [
                $event->order_id,
                $event->product_id,
                $event->name,
                $event->price,
                $event->quantity,
            ]
        );
    }

    public function onOrderItemRemoved(OrderItemRemoved $event): void
    {
        $result = DB::select(
            '
            WITH updated AS (
                UPDATE orderlines
                SET
                    quantity = quantity - ?,
                    updated_at = ?
                WHERE order_id = ?
                    AND product_id = ?
                RETURNING quantity
            )
            DELETE FROM orderlines
            USING updated
            WHERE order_id = ?
                AND product_id = ?
                AND updated.quantity <= 1

            ',
            [
                $event->quantity,
                $event->updated_at,
                $event->order_id,
                $event->product_id,
                $event->order_id,
                $event->product_id
            ]
        );

        if(empty($result)) throw new ProductQuantityTooLowException($event->product_id, $event->order_id, "Product does not found.");
    }
}