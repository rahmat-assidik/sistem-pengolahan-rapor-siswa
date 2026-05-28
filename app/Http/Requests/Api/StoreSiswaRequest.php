<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|unique:siswa,nis|max:20',
            'nama_siswa' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'angkatan' => 'required|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:Aktif,Tidak Aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS harus diisi',
            'nis.unique' => 'NIS sudah terdaftar',
            'nama_siswa.required' => 'Nama siswa harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'angkatan.required' => 'Angkatan harus diisi',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ];
    }
}
