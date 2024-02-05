<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MultipleSavingRequest extends FormRequest
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
            'principal_savings' => ['required', 'array'],
            'mandatory_savings' => ['required', 'array'],
            'special_mandatory_savings' => ['required', 'array'],
            'voluntary_savings' => ['required', 'array'],
            'recretional_savings' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'principal_savings.required' => 'Simpanan pokok tidak boleh kosong dan data yang dikirim harus dalam bentuk yang valid.',
            'mandatory_savings.required' => 'Simpanan tidak boleh kosong dan data yang dikirim harus dalam bentuk yang valid.',
            'special_mandatory_savings.required' => 'Simpanan wajib khusus tidak boleh kosong dan data yang dikirim harus dalam bentuk yang valid.',
            'voluntary_savings.required' => 'Simpanan sukarela tidak boleh kosong dan data yang dikirim harus dalam bentuk yang valid.',
            'recretional_savings.required' => 'Simpanan rekreasi tidak boleh kosong dan data yang dikirim harus dalam bentuk yang valid.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi data gagal',
            'data' => $validator->errors(),
        ]));
    }
}
