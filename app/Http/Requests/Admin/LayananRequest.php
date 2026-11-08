<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150', Rule::unique('layanan', 'nama')->ignore($this->route('layanan'))],
            'deskripsi' => ['nullable', 'string'],
            'durasi_menit' => ['required', 'integer', 'min:1', 'max:1440'],
            'interval_servis_hari' => ['nullable', 'integer', 'min:1'],
            'interval_servis_km' => ['nullable', 'integer', 'min:1'],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }
}
