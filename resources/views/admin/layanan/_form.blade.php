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
            Nama layanan <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-wrench" aria-hidden="true"></i></span>
            <input
                type="text"
                id="{{ $fieldId('nama') }}"
                name="nama"
                value="{{ $useOld ? old('nama', $layanan->nama) : $layanan->nama }}"
                class="form-control{{ $errorsAktif->has('nama') ? ' is-invalid' : '' }}"
                maxlength="150"
                placeholder="contoh: Ganti Oli, Servis Rutin..."
                required
                @if (! $inModal) autofocus @endif>
            @if ($errorsAktif->has('nama'))
                <div class="invalid-feedback">{{ $errorsAktif->first('nama') }}</div>
            @endif
        </div>
    </div>

    <div class="col-12">
        <label for="{{ $fieldId('deskripsi') }}" class="form-label">Deskripsi</label>
        <textarea
            id="{{ $fieldId('deskripsi') }}"
            name="deskripsi"
            rows="3"
            class="form-control{{ $errorsAktif->has('deskripsi') ? ' is-invalid' : '' }}"
            placeholder="Ringkasan pekerjaan yang termasuk dalam layanan ini...">{{ $useOld ? old('deskripsi', $layanan->deskripsi) : $layanan->deskripsi }}</textarea>
        @if ($errorsAktif->has('deskripsi'))
            <div class="invalid-feedback">{{ $errorsAktif->first('deskripsi') }}</div>
        @endif
        <div class="form-text">Ringkasan pekerjaan yang termasuk dalam layanan ini.</div>
    </div>

    <div class="col-12 col-md-4">
        <label for="{{ $fieldId('durasi_menit') }}" class="form-label">
            Durasi (menit) <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-stopwatch" aria-hidden="true"></i></span>
            <input
                type="number"
                id="{{ $fieldId('durasi_menit') }}"
                name="durasi_menit"
                value="{{ $useOld ? old('durasi_menit', $layanan->durasi_menit) : $layanan->durasi_menit }}"
                class="form-control{{ $errorsAktif->has('durasi_menit') ? ' is-invalid' : '' }}"
                min="1"
                max="1440"
                placeholder="60"
                required>
            <span class="input-group-text">menit</span>
            @if ($errorsAktif->has('durasi_menit'))
                <div class="invalid-feedback">{{ $errorsAktif->first('durasi_menit') }}</div>
            @endif
        </div>
    </div>

    <div class="col-12 col-md-4">
        <label for="{{ $fieldId('interval_servis_hari') }}" class="form-label">Interval servis (hari)</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-calendar-alt" aria-hidden="true"></i></span>
            <input
                type="number"
                id="{{ $fieldId('interval_servis_hari') }}"
                name="interval_servis_hari"
                value="{{ $useOld ? old('interval_servis_hari', $layanan->interval_servis_hari) : $layanan->interval_servis_hari }}"
                class="form-control{{ $errorsAktif->has('interval_servis_hari') ? ' is-invalid' : '' }}"
                min="1"
                placeholder="mis. 180">
            <span class="input-group-text">hari</span>
            @if ($errorsAktif->has('interval_servis_hari'))
                <div class="invalid-feedback">{{ $errorsAktif->first('interval_servis_hari') }}</div>
            @endif
        </div>
        <div class="form-text">Kosongkan jika tidak berbasis hari.</div>
    </div>

    <div class="col-12 col-md-4">
        <label for="{{ $fieldId('interval_servis_km') }}" class="form-label">Interval servis (km)</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-map" aria-hidden="true"></i></span>
            <input
                type="number"
                id="{{ $fieldId('interval_servis_km') }}"
                name="interval_servis_km"
                value="{{ $useOld ? old('interval_servis_km', $layanan->interval_servis_km) : $layanan->interval_servis_km }}"
                class="form-control{{ $errorsAktif->has('interval_servis_km') ? ' is-invalid' : '' }}"
                min="1"
                placeholder="mis. 5000">
            <span class="input-group-text">km</span>
            @if ($errorsAktif->has('interval_servis_km'))
                <div class="invalid-feedback">{{ $errorsAktif->first('interval_servis_km') }}</div>
            @endif
        </div>
        <div class="form-text">Kosongkan jika tidak berbasis kilometer.</div>
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
                    @checked($useOld ? old('aktif', $layanan->aktif) : $layanan->aktif)>
                <label class="form-check-label" for="{{ $fieldId('aktif') }}">
                    <span class="fw-semibold">Layanan aktif</span>
                    <span class="text-body-secondary d-block small">Pelanggan dapat memilih layanan ini saat membuat reservasi</span>
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
        <a href="{{ route('admin.layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    @endif
</div>
