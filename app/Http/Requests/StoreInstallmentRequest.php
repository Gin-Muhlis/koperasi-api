<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInstallmentRequest extends FormRequest
{
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
			'member_id' => ['required', 'exists:members,id'],
			'loan_id' => ['required', 'exists:loans,id'],
			'amount' => ['required', 'numeric'],
			'sub_category_id' => ['required', 'exists:sub_categories,id'],
		];
	}

	public function messages(): array {
		return [
			'member_id.required' => 'Data Anggota tidak valid',
			'member_id.exists' => 'Data Anggota tidak valid',
			'loan_id.required' => 'Pinjaman Anggota tidak valid',
			'loan_id.exists' => 'Pinjaman Anggota tidak valid',
			'sub_categori_id.required' => 'Jenis Pinjaman tidak valid',
			'sub_categori_id.exists' => 'Jenis Pinjaman tidak valid',
			'amount.string' => 'Jumlah pembayaran tidak valid',
			'amount.required' => 'Jumlah pembayaran tidak valid',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi data gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
