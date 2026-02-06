<?php

namespace App\Domain\Interfaces;

use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\ValueObjects\OrderId;
use App\Domain\OrderAggregate\ValueObjects\OrderState;
use App\Domain\Shared\Uuid;
use DateTimeImmutable;

interface IOrderRepository
{
    public function getById(OrderId $id) : Order | null;
    public function isExists(OrderId $id) : bool;
    
    /**
     * Get all orders.
     *
     * @return Order[] | null
     */
    public function getAll() : iterable | null;
    
    public function save(Order $order) : OrderId;
    public function update(Order $order) : Order;
    public function delete(OrderId $id) : void;

    public function findOrderStateForCustomer(Uuid $id) : ?OrderState;

    public function findExpiratedOrderDrafts(DateTimeImmutable $now, ?int $limit = 500) : iterable;

    public function updateStateToExpire(OrderId $id): void;
}