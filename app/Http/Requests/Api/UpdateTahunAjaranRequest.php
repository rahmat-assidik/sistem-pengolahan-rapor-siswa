<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'sometimes|required|string|max:50',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_selesai' => 'sometimes|required|date|after:tanggal_mulai',
            'is_aktif' => 'sometimes|required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ];
    }
}
