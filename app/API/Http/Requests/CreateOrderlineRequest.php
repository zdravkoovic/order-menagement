<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateOrderlineRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'exists:order_entities,id'],
            'product_ids' => ['required', 'array'],
            'quantities' => ['required', 'array'],

            'product_ids.*' => ['required', 'integer', 'min:1'],
            'quantities.*'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (
                count($this->input('product_ids', [])) !==
                count($this->input('quantities', []))
            ) {
                $validator->errors()->add(
                    'quantity',
                    'Each product must have exactly one corresponding quantity.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.integer'   => 'Customer ID must be a integer.',

            'order_id.required' => 'Order ID is required.',
            'order_id.*.string'  => 'Order ID must be a string.',

            'quantities.required' => 'Payment method is required.',
            'quantities.*.integer'     => 'Payment method must be one of: cash, card, paypal.',

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
            'products.*.price.min'      => 'Price cannot be negative.',

            'notes.string' => 'Notes must be text.',
            'notes.max'    => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function toCommand(): CreateOrderCommand
    {
        return new CreateOrderCommand(
            $this->input('customer_id'),
            $this->input('amount') ?? null,
            $this->input('payment_method') ?? null,
        );
    }
}
