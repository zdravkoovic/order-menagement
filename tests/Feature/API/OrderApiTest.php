<?php

namespace Tests\Feature\API;

use App\Domain\OrderAggregate\PaymentMethod;
use App\Domain\Shared\Uuid;
use App\Infrastructure\Persistance\Models\OrderEntity;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
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
        
        $expiresAt = OrderEntity::first()->expires_at;
        $this->assertTrue(
            $expiresAt->between(
                now()->addMinutes(30)->subSeconds(5),
                now()->addMinutes(30)->addSeconds(5)
            ),
        );

        $this->assertDatabaseHas('order_entities', ['state' => 'DRAFT']);
        
        $this->assertDatabaseCount('order_entities', 1);
    }
}