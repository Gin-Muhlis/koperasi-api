<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array {
		return [
			'name' => ['required', 'max:100', 'string'],
			'password' => ['required'],
			'email' => ['required', 'email'],
			'address' => ['required', 'string'],
			'phone_number' => ['required', 'max:20', 'string'],
			'position' => ['required', 'in:pns,p3k,cpns'],
			'gender' => ['required', 'in:L,P'],
			'religion' => ['required', 'max:20', 'string'],
			'image' => ['nullable', 'image', 'max:2048'],
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'success' => false,
			'message' => 'Validasi Gagal',
			'data' => $validator->errors(),
		], 422));
	}
}
