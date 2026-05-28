<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|string|unique:guru,nip|max:20',
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'required|string|max:20',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP harus diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama_guru.required' => 'Nama guru harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'no_hp.required' => 'Nomor HP harus diisi',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ];
    }
}
