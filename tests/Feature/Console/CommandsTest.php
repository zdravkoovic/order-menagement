<?php

namespace Tests\Feature\Console;

use App\Infrastructure\Persistance\Models\OrderEntity;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Illuminate\Support\now;

final class CommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_orders_becomes_expired(): void
    {
        $order = OrderEntity::factory()->create([
            'created_at' => new DateTimeImmutable('-40 minutes'),
            'expires_at' => new DateTimeImmutable('-10 minutes')
        ]);

        $this->assertDatabaseHas('order_entities', [
            'id' => $order->id,
            'state' => 'DRAFT'
        ]);

        $this->artisan('order:expire-draft-order')->assertExitCode(0);

        $this->assertDatabaseHas('order_entities', [
            'id' => $order->id,
            'state' => 'EXPIRED'
        ]);
    }
}