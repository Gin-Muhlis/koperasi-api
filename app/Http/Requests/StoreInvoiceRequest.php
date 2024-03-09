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
			'invoice_name' => ['string', 'required', 'max:100'],
			'due_date' => ['required', 'date'],
			'payment_source' => ['required', 'in:gaji pns,gaji p3k,komite,TPP'],
		];
	}

	public function messages(): array {
		return [
			'invoice_name.required' => 'Nama invoice harus diisi.',
			'invoice_name.string' => 'Nama invoice harus berupa teks.',
			'invoice_name.max' => 'Nama invoice tidak boleh lebih dari 100 karakter.',
			'due_date.required' => 'Tanggal jatuh tempo harus diisi.',
			'due_date.date' => 'Tanggal jatuh tempo harus dalam format tanggal yang valid.',
			'payment_source.required' => 'Sumber pembayaran harus dipilih.',
			'payment_source.in' => 'Sumber pembayaran yang dipilih tidak valid.',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
