<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePositionCategoryRequest extends FormRequest
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
            'position' => ['required', 'string'],
            'pokok' => ['required', 'numeric'],
            'wajib' => ['required', 'numeric'],
            'wajib_khusus' => ['required', 'numeric'],
        ];
    }


    public function messages(): array
    {
        return [
            'position.required' => 'Nama posisi tidak boleh kosong',
            'position.string' => 'Nama posisi tidak valid',
            'pokok.required' => 'Jumlah pokok tidak boleh kosong',
            'pokok.number' => 'Jumlah pokok tidak valid',
            'wajib.required' => 'Jumlah wajib tidak boleh kosong',
            'wajib.string' => 'Jumlah wajib tidak valid',
            'wajib_khusus.required' => 'Jumlah wajib khusus tidak boleh kosong',
            'wajib_khusus.string' => 'Jumlah Wajib khusus tidak valid',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422));
    }
}
