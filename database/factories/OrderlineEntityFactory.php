<?php

namespace Database\Factories;

use App\Infrastructure\Persistance\Models\OrderlineEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderlineEntityFactory extends Factory
{
    protected $model = OrderlineEntity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => 1,
            'quantity' => 1,
            'price' => 1
        ];
    }
}
