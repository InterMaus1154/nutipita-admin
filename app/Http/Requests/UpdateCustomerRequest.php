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
            'customer_address' => 'nullable|string|max:300'
        ];
    }
}
