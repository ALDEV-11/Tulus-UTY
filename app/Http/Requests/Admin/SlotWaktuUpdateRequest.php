<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SlotWaktuUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'admin';
    }

    public function rules(): array
    {
        return [
            'kapasitas' => ['required', 'integer', 'min:1', 'max:100'],
            'aktif' => ['required', 'boolean'],
        ];
    }
}
