<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentRequest extends FormRequest {
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
			'invoice_id' => ['required', 'exists:invoices,id'],
			'amount' => ['required', 'numeric'],
			'no_rek' => ['nullable'],
			'transfer_name' => ['nullable', 'string'],
			'total_invoice' => ['required', 'numeric'],
			'image' => ['required', 'image', 'max:2046', 'mimes:jpg,jpeg,png']
		];
	}

	public function messages(): array {
		return [
			'invoice_id.required' => 'Invoice tidak ditemukan.',
			'invoice_id.exists' => 'Invoice tidak valid.',
			'amount.required' => 'Jumlah Pembayaran tidak ditemukan.',
			'amount.numeric' => 'Jumlah Pembayaran tidak valid.',
			'total_invoice.required' => 'Total Invoice tidak ditemukan.',
			'total_invoice.numeric' => 'Total Invoice tidak valid.',
			'image.required' => 'Gambar wajib diunggah.',
			'image.image' => 'Berkas yang diunggah harus berupa gambar.',
			'image.max' => 'Ukuran gambar tidak boleh melebihi 2MB.',
			'image.mimes' => 'Format gambar yang diterima adalah JPG, JPEG, atau PNG.'
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
