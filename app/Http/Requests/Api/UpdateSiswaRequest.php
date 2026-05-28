<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => 'sometimes|required|string|unique:siswa,nis,' . $this->siswa->id . '|max:20',
            'nama_siswa' => 'sometimes|required|string|max:100',
            'jenis_kelamin' => 'sometimes|required|in:Laki-laki,Perempuan',
            'angkatan' => 'sometimes|required|integer|min:2000|max:' . date('Y'),
            'status' => 'sometimes|required|in:Aktif,Tidak Aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.unique' => 'NIS sudah terdaftar',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ];
    }
}
