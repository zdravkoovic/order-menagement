<?php

namespace App\Domain\Interfaces;

use App\Domain\OrderAggregate\ValueObjects\OrderlineId;
use App\Domain\OrderlineAggregate\Orderline;

interface IOrderlineRepository
{
    public function getById(OrderlineId $id) : Orderline | null;
    public function isExists(OrderlineId $id) : bool;
    
    /**
     * Get all orders.
     *
     * @return Orderline[]
     */
    public function getAll() : iterable | null;
    
    public function save(Orderline $orderline) : OrderlineId;
    public function update(Orderline $entity) : Orderline;
    public function delete(OrderlineId $id) : void;
}