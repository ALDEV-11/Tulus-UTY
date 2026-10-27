<?php

namespace App\Http\Requests\Customer;

use App\Models\Kendaraan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->value === 'pelanggan';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('kendaraan_id')) {
            $this->merge(['kendaraan_baru' => null]);

            return;
        }

        $baru = $this->input('kendaraan_baru');

        if (is_array($baru) && isset($baru['plat_nomor']) && is_string($baru['plat_nomor'])) {
            $baru['plat_nomor'] = Kendaraan::normalizePlatNomor($baru['plat_nomor']);
            $this->merge(['kendaraan_baru' => $baru]);
        }
    }

    public function rules(): array
    {
        return [
            'kendaraan_id' => [
                'nullable',
                'integer',
                Rule::exists('kendaraan', 'id')->where('user_id', $this->user()->id),
            ],
            'kendaraan_baru' => ['nullable', 'array:merk_motor_id,plat_nomor,model,tahun'],
            'kendaraan_baru.merk_motor_id' => [
                'required_without:kendaraan_id',
                'nullable',
                'integer',
                Rule::exists('merk_motor', 'id')->where('aktif', true),
            ],
            'kendaraan_baru.plat_nomor' => [
                'required_without:kendaraan_id',
                'nullable',
                'string',
                'max:15',
                Rule::unique('kendaraan', 'plat_nomor'),
            ],
            'kendaraan_baru.model' => [
                'required_without:kendaraan_id',
                'nullable',
                'string',
                'max:100',
            ],
            'kendaraan_baru.tahun' => [
                'required_without:kendaraan_id',
                'nullable',
                'integer',
                'min:1980',
                'max:'.(now()->year + 1),
            ],
            'id_slot' => [
                'required',
                'integer',
                Rule::exists('slot_waktu', 'id')->where(fn ($query) => $query
                    ->where('aktif', true)
                    ->whereDate('tanggal', '>=', today())),
            ],
            'layanan' => ['required', 'array', 'min:1'],
            'layanan.*' => [
                'integer',
                Rule::exists('layanan', 'id')->where('aktif', true),
            ],
            'keluhan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'kendaraan_id.exists' => 'Kendaraan tidak ditemukan atau bukan milik Anda.',
            'kendaraan_baru.merk_motor_id.required_without' => 'Pilih merk kendaraan baru.',
            'kendaraan_baru.merk_motor_id.exists' => 'Merk kendaraan tidak tersedia.',
            'kendaraan_baru.plat_nomor.required_without' => 'Isi nomor polisi kendaraan baru.',
            'kendaraan_baru.plat_nomor.unique' => 'Nomor polisi sudah terdaftar.',
            'kendaraan_baru.plat_nomor.max' => 'Nomor polisi maksimal 15 karakter.',
            'kendaraan_baru.model.required_without' => 'Isi tipe kendaraan baru.',
            'kendaraan_baru.model.max' => 'Tipe kendaraan maksimal 100 karakter.',
            'kendaraan_baru.tahun.required_without' => 'Isi tahun kendaraan baru.',
            'kendaraan_baru.tahun.integer' => 'Tahun kendaraan harus berupa angka.',
            'kendaraan_baru.tahun.min' => 'Tahun kendaraan tidak valid.',
            'kendaraan_baru.tahun.max' => 'Tahun kendaraan tidak valid.',
            'id_slot.required' => 'Pilih jadwal reservasi.',
            'id_slot.exists' => 'Slot yang dipilih tidak tersedia.',
            'layanan.required' => 'Pilih minimal satu layanan.',
            'layanan.min' => 'Pilih minimal satu layanan.',
            'layanan.*.exists' => 'Layanan yang dipilih tidak tersedia.',
            'keluhan.max' => 'Keluhan maksimal 1.000 karakter.',
        ];
    }
}
