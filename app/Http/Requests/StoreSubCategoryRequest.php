<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubCategoryRequest extends FormRequest {
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
			'name' => ['string', 'max:255', 'string'],
			'type' => ['required', 'in:debit,kredit'],
			'category_id' => ['required', 'exists:categories,id'],
		];
	}

	public function messages(): array {
		return [
			'name.string' => 'Field nama harus berupa teks.',
			'name.max' => 'Panjang teks pada field nama tidak boleh melebihi 255 karakter.',
			'type.required' => 'Field tipe harus diisi.',
			'type.in' => 'Nilai pada field tipe harus salah satu dari: debit, kredit.',
			'category_id.required' => 'Field category ID harus diisi.',
			'category_id.exists' => 'Nilai pada field category ID tidak valid.',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
