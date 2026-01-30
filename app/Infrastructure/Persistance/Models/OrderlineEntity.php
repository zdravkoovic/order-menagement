<?php

namespace App\Infrastructure\Persistance\Models;

use Database\Factories\OrderlineEntityFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property string $id
 * @property string $product_id
 * @property int    $quantity
 * @property string $order_id
 * @property-read OrderEntity $order
 *
 * @mixin Eloquent
 */
class OrderlineEntity extends Model
{
    use HasFactory;

    public mixed $amount;

    protected static function newFactory(): OrderlineEntityFactory
    {
        return OrderlineEntityFactory::new();
    }
    /**
     * The table
     *
     * @var string
     */
    protected $table = 'orderline_entities';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'product_id',
        'quantity',
        'order_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderEntity::class, 'order_id', 'id');
    }
}
