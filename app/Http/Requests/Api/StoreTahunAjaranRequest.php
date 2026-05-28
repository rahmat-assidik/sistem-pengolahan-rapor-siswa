<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tahun ajaran harus diisi',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'is_aktif.required' => 'Status aktif harus diisi',
        ];
    }
}
