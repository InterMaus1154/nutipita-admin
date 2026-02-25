<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customer = $this->route('customer');
        return [
            'customer_name' => ['required', 'string', 'max:250',
                Rule::unique('customers', 'customer_name')->ignore($customer->customer_id, 'customer_id')
            ],
            'customer_email' => 'nullable|string|email|max:250',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address_1' => 'required|string|max:300',
            'customer_address_2' => 'nullable|string|max:300',
            'customer_optional_name' => 'nullable|string|max:150',
            'customer_postcode' => 'required|string|max:10',
            'customer_city' => 'required|string|max:100',
            'customer_country' => 'required|string|max:200',
            'customer_business_owner_name' => 'required|string|max:150',
            'customer_trading_name' => 'nullable|string|max:250',
            'customer_delivery_address' => 'nullable|string|max:300',
            'products' => 'array|required',
            'products.*' => 'nullable|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'products.*.min' => 'A product price has to be at least 0 if set'
        ];
    }
}
