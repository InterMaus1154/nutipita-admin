<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Livewire\Attributes\Rule;

class UpdateOrderRequest extends FormRequest
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
            'order_placed_at' => 'required|date',
            'order_due_at' => 'required|date|after_or_equal:order_placed_at',
            'order_status' => ['required', \Illuminate\Validation\Rule::in(array_map(function ($status) {
                return $status->name;
            }, OrderStatus::cases()))],
            'shift' => 'required|in:day,night',
            'products' => 'required|array',
            'product.*' => 'nullable|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'Products are empty or all zero',
            'products.*.min' => 'A product quantity cannot be less than 0'
        ];
    }
}
