<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\UpdateOrder\AddItem\UpdateOrderAddItemCommand;
use Illuminate\Foundation\Http\FormRequest;

final class CreateOrderItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'exists:order_entities,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer'],
            'products.*.quantity' => ['required','integer'],
            'products.*.price' => ['required', 'integer']
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Order must have an id.',
            'products.required' => 'Order must contain at least one item.',

            'products.array'    => 'Products must be provided as an array.',
            'products.min'      => 'Order must have at least one item.',

            'products.*.product_id.required' => 'Each item must have a product ID.',
            'products.*.product_id.integer'   => 'Product ID must be an integer.',

            'products.*.quantity.required' => 'Each item must have a quantity.',
            'products.*.quantity.integer'  => 'Quantity must be an integer.',
            'products.*.quantity.min'      => 'Quantity must be at least 1.',

            'products.*.price.required' => 'Each item must have a price.',
            'products.*.price.numeric'  => 'Price must be a number.',
            'products.*.price.min'      => 'Price cannot be negative.'
        ];
    }

    public function toCommand(): UpdateOrderAddItemCommand
    {
        return new UpdateOrderAddItemCommand(
            $this->input('order_id'),
            $this->input('products'),
        );
    }
}