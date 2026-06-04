<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kelas' => 'sometimes|required|string|unique:kelas,kode_kelas,' . $this->kelas->id . '|max:20',
            'nama_kelas' => 'sometimes|required|string|max:100',
            'tingkat' => 'sometimes|required|string|in:X,XI,XII',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kelas.unique' => 'Kode kelas sudah terdaftar',
            'tingkat.in' => 'Tingkat kelas harus X, XI, atau XII',
        ];
    }
}
