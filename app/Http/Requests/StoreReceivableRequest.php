<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReceivableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'member_id' => ['required', 'exists:members,id'],
			'amount' => ['required', 'numeric'],
			'sub_category_id' => ['required', 'exists:sub_categories,id'],
			'duration' => ['required', 'numeric'],
			'date' => ['required', 'date'],
			'total' => ['required', 'numeric'],
			'deadline' => ['required', 'date'],
			'description' => ['nullable', 'string'],
		];
	}

	public function messages(): array
{
    return [
        'member_id.required' => 'Member tidak valid.',
        'member_id.exists' => 'Member tidak valid.',
        'amount.required' => 'Jumlah Peminjaman harus diisi.',
        'amount.numeric' => 'Jumlah Peminjaman harus berupa angka.',
        'sub_category_id.required' => 'Jenis pinjaman tidak valid.',
        'sub_category_id.exists' => 'Jenis pinjaman tidak valid.',
        'duration.required' => 'Durasi harus diisi.',
        'duration.numeric' => 'Duration tidak valid.',
        'date.required' => 'Tanggal peminjaman harus diisi.',
        'date.date' => 'Tanggal peminjaman harus dalam format tanggal yang valid.',
        'total.required' => 'Total pinjaman harus diisi.',
        'total.numeric' => 'Total pinjaman tidak valid.',
        'deadline.required' => 'Tenggat bayar harus diisi.',
        'deadline.date' => 'Tenggat bayar harus dalam format tanggal yang valid.',
        'description.string' => 'Deskripsi tidak valid.',
    ];
}


	public function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi gagal',
			'errors' => $validator->errors()
		], 422));
	}
}
