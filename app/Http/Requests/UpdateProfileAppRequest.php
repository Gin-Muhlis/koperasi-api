<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
		return true;
	}
    
    public function rules(): array
    {
        return [
            'chairmans_name' => ['required', 'string'],
            'secretary_name' => ['required', 'string'],
            'treasurer_name' => ['required', 'string'],
            'address' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'chairmans_name.required' => 'Nama Ketua diperlukan.',
            'chairmans_name.string' => 'Nama Ketua harus berupa teks.',
            'secretary_name.required' => 'Nama Sekretaris diperlukan.',
            'secretary_name.string' => 'Nama Sekretaris harus berupa teks.',
            'treasurer_name.required' => 'Nama Bendahara diperlukan.',
            'treasurer_name.string' => 'Nama Bendahara harus berupa teks.',
            'address.required' => 'Alamat diperlukan.',
            'address.string' => 'Alamat harus berupa teks.',
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Validasi Gagal',
            'errors' => $validator->errors(),
        ], 422));    
    }
}
