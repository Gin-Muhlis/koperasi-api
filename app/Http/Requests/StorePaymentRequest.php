<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
		];
	}

	public function message(): array {
		return [
			'invoice_id.required' => 'Invoice tidak ditemukan.',
			'invoice_id.exists' => 'Invoice tidak valid.',
			'amount.required' => 'Jumlah Pembayaran tidak ditemukan.',
			'amount.numeric' => 'Jumlah Pembayaran tidak valid.',
			'total_invoice.required' => 'Total Invoice tidak ditemukan.',
			'total_invoice.numeric' => 'Total Invoice tidak valid.',
		];
	}
}
