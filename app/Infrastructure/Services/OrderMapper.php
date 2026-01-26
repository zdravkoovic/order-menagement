<?php

namespace App\Infrastructure\Services;

use App\Domain\OrderAggregate\CustomerId;
use App\Domain\OrderAggregate\Money;
use App\Domain\OrderAggregate\Order;
use App\Domain\OrderAggregate\OrderBuilder;
use App\Domain\OrderAggregate\OrderId;
use App\Domain\OrderAggregate\OrderNumber;
use App\Domain\OrderAggregate\OrderState;
use App\Infrastructure\Persistance\Models\OrderEntity;

use function Laravel\Prompts\info;

class OrderMapper
{
    public function toDomain(OrderEntity $data): Order
    {
        $order = $data->state === OrderState::DRAFT->value
            ? OrderBuilder::draft()
            : OrderBuilder::pending();
        return $order
                    ->forCustomer(CustomerId::fromString($data->customer_id))
                    ->withId(OrderId::fromString($data->id))
                    ->withPaymentMethod($data->payment_method)
                    ->withReference(OrderNumber::fromString($data->reference))
                    ->withTotalAmount(Money::fromFloat($data->total_amount))
                    ->withCreationTime($data->created_at)
                    ->withLastModifiedTime($data->updated_at)
                    ->withExpirationTime($data->expires_at)
                    ->build();
    }

    public function toEntity(Order $order): OrderEntity
    {
        $entity = new OrderEntity();

        $entity->id = $order->id?->value();
        $entity->customer_id = $order->customerId->value();
        $entity->total_amount = $order->totalAmount?->value();
        $entity->payment_method = $order->paymentMethod;   
        $entity->reference = $order->reference?->value();
        $entity->created_at = $order->createdDate;
        $entity->updated_at = $order->lastModifiedDate;
        $entity->expires_at = $order->expiresAt;
        $entity->state = $order->state->value;

        return $entity;
    }
}