<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInvoiceRequest extends FormRequest {
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
			'principal_savings' => ['array'],
			'mandatory_savings' => ['array'],
			'special_mandatory_savings' => ['array'],
			'voluntary_savings' => ['array'],
			'recretional_savings' => ['array'],
			'receivables' => ['array'],
			'accounts_receivable' => ['array'],
			'invoices' => ['array'],
			'month_year' => ['string'],
			'description' => ['string'],
		];
	}

	public function messages(): array {
		return [
			'month_year.required' => 'Data waktu tidak valid',
			'month_year.string' => 'Data waktu tidak valid',
			'description.required' => 'Deskripsi tidak valid',
			'description.string' => 'Deskripsi tidak valid',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
