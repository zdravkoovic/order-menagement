<?php

namespace App\Infrastructure\Persistance\Models;

use App\Domain\OrderAggregate\OrderState;
use App\Domain\OrderAggregate\PaymentMethod;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $customer_id
 * @property float    $total_amount
 * @property string $reference
 * @property OrderState $state
 * @property DateTimeImmutable $expires_at
 * @property PaymentMethod $payment_method
 * @property DateTimeImmutable $created_at
 * @property DateTimeImmutable $updated_at
 * 
 * @mixin \Eloquent
 */
class OrderEntity extends Model
{
    use HasUuids;
    /**
     * The table
     *
     * @var string
     */
    protected $table = 'order_entities';

    protected $fillable = [
        'id',
        'customer_id',
        'total_amount',
        'reference',
        'payment_method',
        'state',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'state' => OrderState::class,
        'expires_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime'
    ];
    
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
    */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
    */
    protected $keyType = 'string';

    public function orderlines()
    {
        return $this->hasMany(OrderlineEntity::class, 'order_id', 'id');
    }
}
    