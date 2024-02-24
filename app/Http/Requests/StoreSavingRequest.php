<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSavingRequest extends FormRequest
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
			'members' => ['required', 'array'],
			'month_year' => ['required', 'string'],
			'sub_category_id' => ['required', 'exists:sub_categories,id'],
			'description' => ['nullable', 'string'],
			'type_saving' => ['required', 'string'],
		];
	}

	public function messages(): array
	{
		return [
			'members_id.required' => 'Data member tidak boleh kosong.',
			'members_id.array' => 'Data member tidak valid.',
			'month_year.required' => 'Waktu simpanan tidak boleh kosong.',
			'month_year.string' => 'Waktu simpanan tidak valid.',
			'sub_category_id.required' => 'Sub Kategori tidak ditemukan.',
			'sub_category_id.exists' => 'Sub Kategori tidak valid.',
			'description.string' => 'Deskripsi tidak valid.',
			'type_saving.required' => 'Jenis Simpanan tidak boleh kosong.',
			'type_saving.string' => 'Jenis Simpanan tidak valid.',
		];
	}

	public function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi gagal',
			'errors' => $validator->errors()
		]));
	}
}
