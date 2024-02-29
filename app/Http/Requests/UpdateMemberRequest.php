<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest {
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
			'email' => [
				'required',
				Rule::unique('members', 'email')->ignore($this->member),
				'email',
			],
			'name' => ['required', 'max:100', 'string'],
			'address' => ['required', 'max:255', 'string'],
			'phone_number' => ['required', 'max:20', 'string'],
			'gender' => ['required', 'in:L,P'],
			'religion' => ['required', 'max:20', 'string'],
			'group_id' => ['required', 'exists:position_categories,id'],
			'position' => ['required', 'in:pns,p3k,cpns'],
			'image' => ['nullable', 'image', 'max:2048'],
			'username' => ['required', 'max:100', 'string'],
			'password' => ['nullable'],
			'active' => ['required', 'boolean'],
			'role' => ['required', 'string']
		];
	}

	public function messages() {
		return [
			'name.required' => 'Nama harus diisi.',
			'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
			'name.string' => 'Format nama tidak valid.',
			'username.required' => 'Nama harus diisi.',
			'username.max' => 'Nama tidak boleh lebih dari 100 karakter.',
			'username.string' => 'Format nama tidak valid.',
			'password.required' => 'Password harus diisi.',
			'email.required' => 'Email wajib diisi.',
			'email.email' => 'Format email tidak valid.',
			'email.unique' => 'Email telah terdaftar',
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
			'group_id.required' => 'Golongan member tidak boleh kosong',
			'group_id.exists' => 'Golongan Member tidak valid',
			'role.required' => 'Role tidak boleh kosong',
			'role.string' => 'Role tidak valid',
		];
	}

	/**
	 * pesan untuk validasi gagal
	 * @param Validator $validator
	 *
	 * @return [type]
	 */
	public function failedValidation(Validator $validator) {
		throw new HttpResponseException(response()->json([
			'message' => 'Validasi Gagal',
			'errors' => $validator->errors(),
		], 422));
	}
}
