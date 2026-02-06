<?php

namespace App\API\Http\Requests;

use App\Application\Order\Commands\CreateOrder\CreateOrderCommand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateOrderlineRequest extends FormRequest
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
            'orderline_id' => ['required', 'integer', 'exists:orderline_entities,id'],
            'quantities' => ['required', 'integer', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'orderline_id.required' => 'Order ID is required.',
            'orderline_id.integer'  => 'Order ID must be a string.',

            'quantities.required' => 'Quantity is required.',
            'quantities.integer'     => 'Quantity must be an integer',
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
