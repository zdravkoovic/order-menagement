<?php

namespace Database\Factories;

use App\Domain\OrderAggregate\OrderState;
use App\Domain\Shared\Uuid;
use App\Infrastructure\Persistance\Models\OrderEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderEntityFactory extends Factory
{
    protected $model = OrderEntity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::generate(),
            'customer_id' => Uuid::generate(),
            'total_amount' => 0.0,
            'payment_method' => "undefined",
            'reference' => "undefined",
            'state' => OrderState::DRAFT->value,
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => null,
        ];
    }
}
