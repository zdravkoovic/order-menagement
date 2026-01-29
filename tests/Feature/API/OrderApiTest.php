<?php

namespace Tests\Feature\API;

use App\Domain\Shared\Uuid;
use App\Infrastructure\Persistance\Models\OrderEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_for_guests(): void
    {

        $customer_id = Uuid::generate()->__toString();
        Http::fake([
            config('services.customer.uri') . "*" => Http::response(['id' => $customer_id ], 200),
        ]);
        $payload = [
            'customer_id' => Uuid::generate()->__toString(),
            'is_guest' => true
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        
        $order = OrderEntity::first();
        $this->assertTrue(
            abs(
                $order->expires_at->getTimestamp()
                - $order->created_at->addMinutes(30)->getTimestamp()
            ) <= 2
        );

        $this->assertDatabaseHas('order_entities', ['state' => 'DRAFT']);
        
        $this->assertDatabaseCount('order_entities', 1);
    }

    public function test_create_order_for_registered_users(): void
    {
        $customer_id = Uuid::generate()->__toString();
        Http::fake([
            config('services.customer.uri') . "*" => Http::response(['id' => $customer_id], 200),
        ]);
        $payload = [
            'customer_id' => Uuid::generate()->__toString(),
            'is_guest' => false
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201);

        $order = OrderEntity::first();
        $this->assertTrue(
            abs(
                $order->expires_at->getTimestamp()
                - $order->created_at->addMinutes(120)->getTimestamp()
            ) <= 2
        );

        $this->assertDatabaseHas('order_entities', ['state' => 'DRAFT']);
        
        $this->assertDatabaseCount('order_entities', 1);
    }
}