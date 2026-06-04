<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas|max:20',
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|string|in:X,XI,XII',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kelas.required' => 'Kode kelas harus diisi',
            'kode_kelas.unique' => 'Kode kelas sudah terdaftar',
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'tingkat.required' => 'Tingkat kelas harus diisi',
            'tingkat.in' => 'Tingkat kelas harus X, XI, atau XII',
        ];
    }
}
