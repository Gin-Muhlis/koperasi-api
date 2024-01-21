<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest {
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
			'name' => ['required', 'max:100', 'string'],
			'email' => ['required', 'email'],
			'phone_number' => ['required', 'max:20', 'string'],
			'address' => ['required', 'string'],
			'gender' => ['required', 'in:L,P'],
			'religion' => ['required', 'max:20', 'string'],
			'position' => ['required', 'in:pns,p3k,cpns'],
			'image' => ['nullable', 'image', 'max:2048'],
			'password' => ['required'],
		];
	}

	public function messages() {
		return [
			'name.required' => 'Nama harus diisi.',
			'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
			'name.string' => 'Format nama tidak valid.',
			'password.required' => 'Password harus diisi.',
			'email.required' => 'Email wajib diisi.',
			'email.email' => 'Format email tidak valid.',
			'address.required' => 'Alamat harus diisi.',
			'address.string' => 'Format alamat tidak valid.',
			'phone_number.required' => 'Nomor telepon harus diisi.',
			'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
			'phone_number.string' => 'Format nomor telepon tidak valid.',
			'position.required' => 'Posisi harus diisi.',
			'position.in' => 'Posisi yang dipilih tidak valid.',
			'gender.required' => 'Jenis kelamin harus diisi.',
			'gender.in' => 'Jenis kelamin yang dipilih tidak valid.',
			'religion.required' => 'Agama harus diisi.',
			'religion.max' => 'Agama tidak boleh lebih dari 20 karakter.',
			'religion.string' => 'Format agama tidak valid.',
			'image.nullable' => 'Format gambar tidak valid.',
			'image.image' => 'Format file harus berupa gambar.',
			'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
		];
	}

	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'success' => false,
			'message' => 'Validasi Gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
