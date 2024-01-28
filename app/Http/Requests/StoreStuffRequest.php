<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStuffRequest extends FormRequest {
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
			'name' => ['required', 'max:255', 'string'],
			'price' => ['required', 'numeric'],
			'image' => ['required', 'image', 'max:2048'],
			'product_id' => ['required', 'exists:products,id'],
		];
	}

	public function messages(): array {
		return [
			'name.required' => 'Field nama harus diisi.',
			'name.max' => 'Panjang teks pada field nama tidak boleh melebihi 255 karakter.',
			'name.string' => 'Field nama harus berupa teks.',
			'price.required' => 'Field harga harus diisi.',
			'price.numeric' => 'Field harga harus berupa angka.',
			'image.required' => 'Field gambar harus diisi.',
			'image.image' => 'Field gambar harus berupa file gambar.',
			'image.max' => 'Ukuran gambar tidak boleh melebihi 2048 kilobita.',
			'product_id.required' => 'Field ID produk harus diisi.',
			'product_id.exists' => 'Nilai pada field ID produk tidak valid.',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'data' => $validator->errors(),
		]));
	}
}
