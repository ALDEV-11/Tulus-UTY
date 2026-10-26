<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MerkMotorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required', 'string', 'max:100',
                Rule::unique('merk_motor', 'nama')->ignore($this->route('merk_motor')),
            ],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }
}
