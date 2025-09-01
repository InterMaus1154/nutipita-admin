<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fin_cat_name' => 'required|string|max:100'
        ];
    }

    public function messages(): array
    {
        return[
            'fin_cat_name.required' => 'The category name is required',
            'fin_cat_name.max' => 'The category name cannot exceed 100 characters'
        ];
    }
}
