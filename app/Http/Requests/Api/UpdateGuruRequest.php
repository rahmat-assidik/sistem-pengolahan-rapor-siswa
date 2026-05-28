<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => 'sometimes|required|string|unique:guru,nip,' . $this->guru->id . '|max:20',
            'nama_guru' => 'sometimes|required|string|max:100',
            'jenis_kelamin' => 'sometimes|required|in:Laki-laki,Perempuan',
            'no_hp' => 'sometimes|required|string|max:20',
            'status' => 'sometimes|required|in:Aktif,Tidak Aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nip.unique' => 'NIP sudah terdaftar',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ];
    }
}
