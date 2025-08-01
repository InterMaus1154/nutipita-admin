<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,customer_id',
            'products' => 'required|array',
            'products.*' => 'integer',
            'order_placed_at' => 'required|date',
            'order_due_at' => 'required|date|after_or_equal:order_placed_at',
            'is_daytime' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'You need to select a customer from the list!',
            'products.required' => 'Select a customer to be able to add products!'
        ];
    }
}
