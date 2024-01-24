<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest {
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
			'email' => [
				'required',
				Rule::unique('members', 'email')->ignore($this->member),
				'email'],
			'name' => ['required', 'max:100', 'string'],
			'address' => ['required', 'max:255', 'string'],
			'phone_number' => ['required', 'max:20', 'string'],
			'gender' => ['required', 'in:L,P'],
			'religion' => ['required', 'max:20', 'string'],
			'image' => ['nullable', 'image', 'max:2048'],
			'username' => ['required', 'max:100', 'string'],
			'password' => ['nullable'],
			'active' => ['required', 'boolean'],
		];
	}

	/**
	 * pesan untuk validasi gagal
	 * @param Validator $validator
	 *
	 * @return [type]
	 */
	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi Gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
