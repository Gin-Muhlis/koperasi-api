<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDetailInvoiceRequest extends FormRequest {
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
			'members' => ['required', 'array'],
			'month_year' => ['string'],
			'description' => ['string'],
			'invoice_id' => ['exists:invoices,id'],
		];
	}

	public function messages(): array {
		return [
			'members.required' => 'Data anggota tidak valid',
			'members.array' => 'Data anggota tidak valid',
			'month_year.required' => 'Data waktu tidak valid',
			'month_year.string' => 'Data waktu tidak valid',
			'description.required' => 'Deskripsi tidak valid',
			'description.string' => 'Deskripsi tidak valid',
			'invoice_id.exists' => 'Invoice tidak valid',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}