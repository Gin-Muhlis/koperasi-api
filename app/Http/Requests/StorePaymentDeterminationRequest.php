<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentDeterminationRequest extends FormRequest
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
            'members_id' => ['required', 'array'],
            'members_id.*' => ['exists:members,id'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'amount' => ['required', 'numeric'],
            'payment_month' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'members_id.required' => 'Mohon pilih minimal satu anggota.',
            'members_id.array' => 'Mohon pastikan anggota yang dipilih dalam bentuk array.',
            'members_id.*.exists' => 'Maaf, salah satu anggota yang dipilih tidak terdaftar.',
            'sub_category_id.required' => 'Harap pilih sub-kategori terlebih dahulu.',
            'sub_category_id.exists' => 'Maaf, sub-kategori yang dipilih tidak ditemukan.',
            'amount.required' => 'Mohon isi kolom jumlah dengan benar.',
            'amount.numeric' => 'Maaf, nilai pada kolom jumlah harus berupa angka.',
            'payment_month.required' => 'Harap tentukan bulan pembayaran.',
            'payment_month.string' => 'Maaf, format bulan pembayaran seharusnya berupa teks.',
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
