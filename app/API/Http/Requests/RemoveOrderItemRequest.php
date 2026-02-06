<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\UpdateOrder\RemoveItem\UpdateOrderRemoveItemCommand;
use Illuminate\Foundation\Http\FormRequest;

final class RemoveOrderItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer'],
            'products.*.quantity' => ['required', 'integer']
        ];
    }
    public function messages(): array
    {
        return [
            'products.required' => 'At least one product is required in order to remove product from your cart.',
            'products.array' => 'Products must be represented as an array regardless it has just one item.',
            'products.*.product_id.required' => 'Product id must be provided.',
            'products.*.product_id.integer' => 'Product id must be unsigned integer.',
            'products.*.quantity.required' => 'Quantity is required.',
            'products.*.quantity.integer' => 'Quantity number must be represented as an integer.'
        ];
    }
}