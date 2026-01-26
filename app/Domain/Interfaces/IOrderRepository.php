<?php

namespace App\Domain\Interfaces;

use App\Domain\OrderAggregate\CustomerId;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\OrderAggregate\OrderState;
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
    
    public function save(Order $id) : OrderId;
    public function update(Order $id) : Order;
    public function delete(OrderId $id) : void;

    public function findOrderStateForCustomer(CustomerId $id) : ?OrderState;

    public function findExpiratedOrderDrafts(DateTimeImmutable $now, ?int $limit = 500) : iterable;

    public function updateStateToExpire(OrderId $id);
}