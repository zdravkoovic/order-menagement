<?php

namespace Test\Feature\Persistance;

use App\Domain\Shared\Uuid;
use App\Infrastructure\Persistance\Models\OrderEntity;
use App\Infrastructure\Persistance\Models\OrderlineEntity;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class OrderPersistanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_exist_without_orderlines(): void
    {
        $order = OrderEntity::factory()->create();

        $this->assertDatabaseHas('order_entities', [
            'id' => $order->id,
            'customer_id' => $order->customer_id,
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
            'reference' => $order->reference,
            'state' => $order->state,
            'expires_at' => $order->expires_at,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at
        ]);

        $this->assertDatabaseCount('orderline_entities', 0);
    }

    public function test_orderline_cannot_exist_without_order(): void
    {
        $this->expectException(QueryException::class);
        OrderlineEntity::factory()->create([
            'product_id' => 1,
            'quantity' => 2,
            'order_id' => 'non-existent-id', // violates FK
        ]);
    }

    public function test_deleting_order_removes_all_orderlines() : void
    {
        $order = OrderEntity::factory()
        ->has(OrderlineEntity::factory()->count(2), 'orderlines')
        ->create();

        $this->assertDatabaseHas('orderline_entities', [
            'order_id' => $order->id
        ]);

        $order->delete();

        $this->assertDatabaseCount('orderline_entities', 0);
    }

    public function test_duplicate_draft_order_is_prevented_by_db()
    {
        $this->expectException(UniqueConstraintViolationException::class);
        $customerId = Uuid::generate();
        OrderEntity::factory()->create([
            'customer_id' => $customerId
        ]);
        
        OrderEntity::factory()->create([
            'customer_id' => $customerId
        ]);  

        $this->assertDatabaseCount('order_entities', 1);
    }
}