<?php

namespace App\Http\Requests\Customer;

use App\Models\Kendaraan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KendaraanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'pelanggan';
    }

    protected function prepareForValidation(): void
    {
        $platNomor = $this->input('plat_nomor');

        if (is_string($platNomor)) {
            $this->merge([
                'plat_nomor' => Kendaraan::normalizePlatNomor($platNomor),
            ]);
        }
    }

    public function rules(): array
    {
        $kendaraan = $this->route('kendaraan');

        return [
            'merk_motor_id' => [
                'required',
                'integer',
                Rule::exists('merk_motor', 'id')->where('aktif', true),
            ],
            'plat_nomor' => [
                'required',
                'string',
                'max:15',
                Rule::unique('kendaraan', 'plat_nomor')->ignore(
                    $kendaraan instanceof Kendaraan ? $kendaraan->id : null
                ),
            ],
            'model' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
        ];
    }

    public function messages(): array
    {
        return [
            'merk_motor_id.required' => 'Pilih merk kendaraan.',
            'merk_motor_id.exists' => 'Merk kendaraan tidak tersedia.',
            'plat_nomor.required' => 'Isi nomor polisi kendaraan.',
            'plat_nomor.max' => 'Nomor polisi maksimal 15 karakter.',
            'plat_nomor.unique' => 'Nomor polisi sudah terdaftar.',
            'model.required' => 'Isi tipe kendaraan.',
            'model.max' => 'Tipe kendaraan maksimal 100 karakter.',
            'tahun.required' => 'Isi tahun kendaraan.',
            'tahun.integer' => 'Tahun kendaraan harus berupa angka.',
            'tahun.min' => 'Tahun kendaraan tidak valid.',
            'tahun.max' => 'Tahun kendaraan tidak valid.',
        ];
    }
}
