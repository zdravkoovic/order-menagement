<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer'],
            'products.*.quantity' => ['required','integer'],
            'notes' => ['string', 'max:255']
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'Order must contain at least one item.',
            'products.array'    => 'Products must be provided as an array.',
            'products.min'      => 'Order must have at least one item.',
   
            'products.*.quantity.required' => 'Quantity is required.',
            'products.*.quantity.integer'   => 'Product quantity must be an integer.',

            'products.*.product_id.required' => 'Each item must have a product ID.',
            'products.*.product_id.integer'   => 'Product ID must be an integer.',

            'notes.string' => 'Notes must be text.',
            'notes.max'    => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function toCommand(): CreateOrderCommand
    {
        return new CreateOrderCommand(
            $this->input('customer_id'),
            $this->input('amount') ?? null,
            $this->input('products') ?? [],
            $this->input('payment_method') ?? null
        );
    }
}
