<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'customer_name' => 'required|string|unique:customers,customer_name|max:250',
            'customer_email' => 'nullable|string|email|max:250',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address_1' => 'required|string|max:300',
            'customer_address_2' => 'nullable|string|max:300',
            'customer_postcode' => 'required|string|max:10',
            'customer_city' => 'required|string|max:100',
            'customer_country' => 'required|string|max:200'
        ];
    }
}
