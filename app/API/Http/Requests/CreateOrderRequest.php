<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use App\Domain\OrderAggregate\ValueObjects\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateOrderRequest extends FormRequest
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
            'customer_id' => ['required', 'string'],
            'is_guest'    => ['required', 'boolean'],
            'amount'      => ['numeric', 'min:0'],
            'payment_method' => [new Enum(PaymentMethod::class)],
            'products' => ['array', 'min:0'],
            'products.*.product_id' => ['integer'],
            'products.*.quantity' => ['integer'],
            'products.*.price' => ['integer'],

            // 'products'       => ['required', 'array', 'min:1'],
            // 'products.*.product_id' => ['required', 'integer'],
            // 'products.*.quantity'   => ['required', 'integer', 'min:1'],
            // 'products.*.price'      => ['required', 'numeric', 'min:0'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }


    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.string'   => 'Customer ID must be a string.',

            'is_guest.required' => 'Guest status is required.',
            'is_guest.boolean'  => 'Guest status must be true or false.',

            'amount.required' => 'Amount is required.',
            'amount.numeric'  => 'Amount must be a number.',
            'amount.min'      => 'Amount cannot be negative.',

            'payment_method.required' => 'Payment method is required.',
            'payment_method.enum'     => 'Payment method must be one of: cash, card, paypal.',

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
