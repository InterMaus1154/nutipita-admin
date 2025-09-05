<?php

namespace App\Http\Requests;

use App\Enums\FinancialRecordType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancialRecordRequest extends FormRequest
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
            'fin_record_name' => 'required|string|max:500',
            'fin_record_amount' => 'required|numeric',
            'fin_record_date' => 'required|date',
            'fin_record_type' => ['required', Rule::in(
                array_map(function (FinancialRecordType $type) {
                    return $type->name;
                }, FinancialRecordType::cases()))],
            'fin_cat_id' => 'nullable|exists:financial_categories,fin_cat_id'
        ];
    }
}
