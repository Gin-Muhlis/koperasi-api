<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentDeterminationRequest extends FormRequest
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
            'members_id' => ['required', 'array'],
            'members_id.*' => ['exists:members,id'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'amount' => ['required', 'numeric'],
            'payment_month' => ['required', 'string']
        ];
    }
}
