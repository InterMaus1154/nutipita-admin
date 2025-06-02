<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStandingOrderRequest extends FormRequest
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
            'customer_id' => ['required', Rule::exists('customers', 'customer_id')],
            'start_from' => 'required|date',
            'products' => 'required|array'
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Select a customer!',
            'start_from.required' => "Select a start date!"
        ];
    }
}
