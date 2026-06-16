<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMapelRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'kode_mapel' => 'sometimes|required|string|unique:mapel,kode_mapel,' . $this->mapel->id . '|max:20',
            'nama_mapel' => 'sometimes|required|string|max:100',
            'kelompok' => 'nullable|in:Wajib,Peminatan,Muatan Lokal',
            'status' => 'nullable|in:Aktif,Tidak Aktif',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'kode_mapel.required' => 'Kode mata pelajaran harus diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah terdaftar',
            'kode_mapel.max' => 'Kode mata pelajaran maksimal 20 karakter',
            'nama_mapel.required' => 'Nama mata pelajaran harus diisi',
            'nama_mapel.max' => 'Nama mata pelajaran maksimal 100 karakter',
            'kelompok.in' => 'Kelompok harus salah satu dari: Wajib, Peminatan, atau Muatan Lokal',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ];
    }
}
