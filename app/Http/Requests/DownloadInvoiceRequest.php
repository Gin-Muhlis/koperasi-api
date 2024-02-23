<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadInvoiceRequest extends FormRequest {
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
			'data' => ['required', 'array'],
			'time_invoice' => ['required', 'string'],
		];
	}

	public function message(): array {
		return [
			'data.required' => 'Data Invoice tidak valid',
			'data.array' => 'Data Invoice tidak valid',
			'time_invoice.required' => 'Waktu invoice tidak ditemukan',
			'time_invoice.string' => 'Waktu invoice tidak valid',
		];
	}
}
