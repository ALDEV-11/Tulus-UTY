<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'admin';
    }

    public function rules(): array
    {
        $pelanggan = $this->route('pelanggan');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($pelanggan),
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => [
                $pelanggan ? 'nullable' : 'required',
                'string', 'min:8', 'confirmed',
            ],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }
}
