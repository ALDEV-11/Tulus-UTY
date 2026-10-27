@php
    $prefix = $prefix ?? '';
    $inModal = $inModal ?? false;
    $useOld = $useOld ?? true;
    $errorsAktif = $useOld ? $errors : new \Illuminate\Support\MessageBag();
    $fieldId = static fn (string $field): string => $prefix.$field;
@endphp

@csrf
@method('PUT')

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Tanggal</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-calendar" aria-hidden="true"></i></span>
            <p class="form-control mb-0">{{ $slot->tanggal->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Jam</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-time-five" aria-hidden="true"></i></span>
            <p class="form-control mb-0">
                {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
            </p>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $fieldId('kapasitas') }}" class="form-label">
            Kapasitas <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-group" aria-hidden="true"></i></span>
            <input
                type="number"
                id="{{ $fieldId('kapasitas') }}"
                name="kapasitas"
                value="{{ $useOld ? old('kapasitas', $slot->kapasitas) : $slot->kapasitas }}"
                class="form-control{{ $errorsAktif->has('kapasitas') ? ' is-invalid' : '' }}"
                min="1"
                max="100"
                required>
            @if ($errorsAktif->has('kapasitas'))
                <div class="invalid-feedback">{{ $errorsAktif->first('kapasitas') }}</div>
            @endif
        </div>
        <div class="form-text">
            <i class="icon-base bx bx-info-circle me-1" aria-hidden="true"></i>
            Reservasi aktif saat ini: <strong>{{ $slot->reservasi_aktif_count }}</strong>. Kapasitas tidak boleh kurang dari jumlah reservasi aktif.
        </div>
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="aktif" value="0">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                name="aktif"
                value="1"
                id="{{ $fieldId('aktif') }}"
                @checked($useOld ? old('aktif', $slot->aktif) : $slot->aktif)>
            <label class="form-check-label" for="{{ $fieldId('aktif') }}">Slot aktif dan dapat dipesan</label>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary" id="{{ $fieldId('btn-simpan-slot') }}">
        <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan
    </button>
    @if ($inModal)
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
    @else
        <a href="{{ route('admin.slot-waktu.index') }}" class="btn btn-outline-secondary">Batal</a>
    @endif
</div>
