<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProfileAppRequest extends FormRequest
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
            'app_name' => ['required', 'string'],
            'chairmans_name' => ['required', 'string'],
            'treasurer_name' => ['required', 'string'],
            'icon' => ['nullable', 'image', 'max:2024'],
            'address' => ['required', 'string'],
            'phone_number' => ['required', 'numeric']
        ];
    }

    public function messages()
    {
        return [
            'app_name.required' => 'Nama diperlukan.',
            'app_name.string' => 'Nama harus berupa teks.',
            'chairmans_name.required' => 'Nama diperlukan.',
            'chairmans_name.string' => 'Nama harus berupa teks.',
            'treasurer_name.required' => 'Nama diperlukan.',
            'treasurer_name.string' => 'Nama harus berupa teks.',
            'icon.required' => 'Icon diperlukan.',
            'icon.image' => 'Icon harus berupa file gambar.',
            'icon.max' => 'Ukuran icon tidak boleh melebihi 2024 kilobita.',
            'address.required' => 'Alamat diperlukan.',
            'address.string' => 'Alamat harus berupa teks.',
            'phone_number.required' => 'Nomor telepon diperlukan.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.'
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi Gagal',
            'errors' => $validator->errors(),
        ], 422));    
    }
}
