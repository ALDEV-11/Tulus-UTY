@php
    $prefix = $prefix ?? '';
    $inModal = $inModal ?? false;
    $useOld = $useOld ?? true;
    $errorsAktif = $useOld ? $errors : new \Illuminate\Support\MessageBag();
    $fieldId = static fn (string $field): string => $prefix.$field;
@endphp

@csrf

<div class="row g-4">
    <div class="col-12">
        <label for="{{ $fieldId('nama') }}" class="form-label">
            Nama merk <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-motorcycle" aria-hidden="true"></i></span>
            <input
                type="text"
                id="{{ $fieldId('nama') }}"
                name="nama"
                value="{{ $useOld ? old('nama', $merk->nama) : $merk->nama }}"
                class="form-control{{ $errorsAktif->has('nama') ? ' is-invalid' : '' }}"
                maxlength="100"
                placeholder="contoh: Honda, Yamaha, Suzuki..."
                required
                @if (! $inModal) autofocus @endif>
            @if ($errorsAktif->has('nama'))
                <div class="invalid-feedback">{{ $errorsAktif->first('nama') }}</div>
            @endif
        </div>
        <div class="form-text">Nama merk kendaraan yang akan tersedia untuk pelanggan.</div>
    </div>

    <div class="col-12">
        <div class="card border-0 bg-body-secondary rounded p-3">
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="aktif" value="0">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    name="aktif"
                    value="1"
                    id="{{ $fieldId('aktif') }}"
                    @checked($useOld ? old('aktif', $merk->aktif) : $merk->aktif)>
                <label class="form-check-label" for="{{ $fieldId('aktif') }}">
                    <span class="fw-semibold">Merk aktif</span>
                    <span class="text-body-secondary d-block small">Pelanggan dapat memilih merk ini saat mendaftarkan kendaraan</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan
    </button>
    @if ($inModal)
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
    @else
        <a href="{{ route('admin.merk-motor.index') }}" class="btn btn-outline-secondary">Batal</a>
    @endif
</div>
