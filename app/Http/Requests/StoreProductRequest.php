<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'product_name' => 'required|string|max:200',
            'product_unit_price' => 'required|decimal:0,2',
            'product_pack_price' => 'nullable|decimal:0,2',
            'product_weight_g' => 'nullable|numeric',
            'product_qty_per_pack' => 'nullable|numeric'
        ];
    }
}
