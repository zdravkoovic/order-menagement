<?php

namespace App\Infrastructure\Persistance\Models;

use Database\Factories\OrderlineEntityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property string $id
 * @property string $product_id
 * @property int    $quantity
 * @property string $order_id
 * @property-read OrderEntity $order
 *
 * @mixin \Eloquent
 */
class OrderlineEntity extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return OrderlineEntityFactory::new();
    }
    /**
     * The table
     *
     * @var string
     */
    protected $table = 'orderlines';

    // No auto-incrementing ID
    public $incrementing = false;

    // No single primary key
    protected $primaryKey = null;

    // UUID/string keys
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'price',
        'order_name',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime'
    ];

    public function order()
    {
        return $this->belongsTo(OrderEntity::class, 'order_id', 'id');
    }
}
