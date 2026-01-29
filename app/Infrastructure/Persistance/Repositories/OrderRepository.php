<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\Interfaces\IOrderRepository;
use App\Domain\OrderAggregate\CustomerId;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\OrderAggregate\OrderState;
use App\Infrastructure\Errors\DuplicateDraftOrderByCustomerException;
use App\Infrastructure\Persistance\Models\OrderEntity;
use App\Infrastructure\Services\OrderMapper;
use Carbon\Carbon;
use DateTimeImmutable;
use PDOException;

class OrderRepository implements IOrderRepository
{
    public function __construct(private OrderMapper $mapper)
    {}

    public function getById(OrderId $id) : Order | null
    {
        $order = OrderEntity::find($id->value());
        return $this->mapper->toDomain($order);
    }

    public function isExists(OrderId $id) : bool
    {
        return OrderEntity::find($id->value()) != null;
    }
    
    /**
     * Get all orders.
     *
     * @return Order[] | null$
    */
    public function getAll() : iterable | null
    {
        return OrderEntity::all();
    }

    public function save(Order $order) : OrderId
    {
        try {
            
            $order = $this->mapper->toEntity($order);
            $order->save();
            return OrderId::fromString($order->id);

        } catch (PDOException $e) {
            
            if($this->isDraftUniqueViolation($e)){
                throw new DuplicateDraftOrderByCustomerException([
                    'customerId' => $order->customerId,
                    'expiresAt' => $order->expiresAt
                ]);
            }
            throw $e;
        }
    }

    public function update(Order $order) : Order
    {
        /** @var OrderEntity $orderEntity */
        $orderEntity = OrderEntity::where('id', $order->id->value())->first();

        $orderEntity->customer_id = $order->customerId->value();
        $orderEntity->total_amount = $order->totalAmount->value();
        $orderEntity->payment_method = $order->paymentMethod->value;
        $orderEntity->reference = $order->reference->value();
        $orderEntity->updated_at = Carbon::now();

        $orderEntity->save();

        return $this->mapper->toDomain($orderEntity);
    }

    public function delete(OrderId $id) : void
    {
        OrderEntity::destroy($id->value());
    }

    public function findOrderStateForCustomer(CustomerId $id): ?OrderState
    {
        $state = OrderEntity::where('customer_id', $id->value())
                ->orderBy('created_at', 'desc')
                ->value('state');
        return $state ? OrderState::from($state->value) : null;
    }

    private function isDraftUniqueViolation(PDOException $e) : bool
    {
        $info = $e->errorInfo ?? null;

        if(is_array($info)) {
            $sqlState = $info[0] ?? null;
            $driverCode = $info[1] ?? null;
            $driverMessage = $info[2] ?? $e->getMessage();

            if($sqlState === '23505') {
                return $this->messageContainsConstraint($driverMessage, 'draft_customer_id_unique');
            }
        }

        $msg = $e->getMessage();
        // Fallback: look for index/column name or duplicate wording in message
        $msg = $e->getMessage();
        if ($this->messageContainsConstraint($msg, 'draft_customer_id_unique')) {
            return true;
        }

        return (bool) preg_match('/duplicate entry|unique constraint|already exists/i', $msg);
    }

    public function findExpiratedOrderDrafts(DateTimeImmutable $now, ?int $limit = 500) : iterable
    {
        return OrderEntity::where('state', OrderState::DRAFT->value)
            ->where('expires_at','<=',$now)
            ->orderBy('expires_at')
            ->limit($limit)
            ->get()
            ->map(fn ($entity) => $this->mapper->toDomain($entity));
    }

    public function updateStateToExpire(OrderId $id): void
    {
        $entity = OrderEntity::find($id->value());
        $entity->state = OrderState::EXPIRED;
        $entity->save();
    }

    private function messageContainsConstraint(string $driverMessage, string $constraintName) : bool
    {
        return stripos($driverMessage, $constraintName) !== false;
    }
}