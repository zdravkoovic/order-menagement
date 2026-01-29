<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\Interfaces\IOrderlineRepository;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderlineAggregate\Orderline;
use App\Domain\OrderlineAggregate\OrderlineId;
use App\Infrastructure\Persistance\Models\OrderlineEntity;
use App\Infrastructure\Services\OrderlineMapper;

class OrderlineRepository implements IOrderlineRepository
{
    public function __construct()
    {}

    public function getById(OrderlineId $id) : Orderline | null
    {
        $orderline = OrderlineEntity::find($id->value());
        return OrderlineMapper::toDomain($orderline);
    }

    public function isExists(OrderlineId $id) : bool
    {
        return OrderlineEntity::find($id->value()) != null;
    }
    
    /**
     * Get all orders.
     *
     * @return Order[] | null$
    */
    public function getAll() : iterable | null
    {
        return OrderlineEntity::all();
    }

    public function save(Orderline $orderline) : OrderlineId
    {
        $orderlineEntity = OrderlineEntity::create([
            'product_id' => $orderline->productId->value(),
            'quantity' => $orderline->quantity->value(),
            'order_id' => $orderline->orderId->value(),
        ]);
        return OrderlineId::fromInt($orderlineEntity->id);
    }
    public function update(Orderline $entity) : Orderline
    {
        /** @var OrderlineEntity $orderlineEntity */
        $orderlineEntity = OrderlineEntity::where('id', $entity->id->value())->first();

        $orderlineEntity->product_id = $entity->productId;
        $orderlineEntity->quantity = $entity->quantity->value();

        $orderlineEntity->save();

        return OrderlineMapper::toDomain($orderlineEntity);
    }

    public function delete(OrderlineId $id) : void
    {
        OrderlineEntity::destroy($id->value());
    }
}