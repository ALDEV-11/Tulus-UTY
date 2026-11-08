@php
    $prefix = $prefix ?? '';
    $inModal = $inModal ?? false;
    $useOld = $useOld ?? true;
    $defaultTanggalMulai = $defaultTanggalMulai ?? null;
    $defaultTanggalSelesai = $defaultTanggalSelesai ?? $defaultTanggalMulai;
    $errorsAktif = $useOld ? $errors : new \Illuminate\Support\MessageBag();
    $fieldId = static fn (string $field): string => $prefix.$field;
@endphp

@csrf

<div class="mb-5">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="avatar avatar-xs">
            <span class="avatar-initial rounded-circle bg-label-primary">1</span>
        </div>
        <h6 class="mb-0">Rentang Tanggal</h6>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="{{ $fieldId('tanggal_mulai') }}" class="form-label">
                Tanggal mulai <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-calendar" aria-hidden="true"></i></span>
                <input
                    type="date"
                    id="{{ $fieldId('tanggal_mulai') }}"
                    name="tanggal_mulai"
                    value="{{ $useOld ? old('tanggal_mulai', $defaultTanggalMulai) : $defaultTanggalMulai }}"
                    class="form-control{{ $errorsAktif->has('tanggal_mulai') ? ' is-invalid' : '' }}"
                    required>
                @if ($errorsAktif->has('tanggal_mulai'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('tanggal_mulai') }}</div>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-6">
            <label for="{{ $fieldId('tanggal_selesai') }}" class="form-label">
                Tanggal selesai <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-calendar-check" aria-hidden="true"></i></span>
                <input
                    type="date"
                    id="{{ $fieldId('tanggal_selesai') }}"
                    name="tanggal_selesai"
                    value="{{ $useOld ? old('tanggal_selesai', $defaultTanggalSelesai) : $defaultTanggalSelesai }}"
                    class="form-control{{ $errorsAktif->has('tanggal_selesai') ? ' is-invalid' : '' }}"
                    required>
                @if ($errorsAktif->has('tanggal_selesai'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('tanggal_selesai') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="avatar avatar-xs">
            <span class="avatar-initial rounded-circle bg-label-primary">2</span>
        </div>
        <h6 class="mb-0">Jam Operasional & Kapasitas</h6>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-3">
            <label for="{{ $fieldId('jam_mulai') }}" class="form-label">
                Jam mulai <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-time" aria-hidden="true"></i></span>
                <input
                    type="time"
                    id="{{ $fieldId('jam_mulai') }}"
                    name="jam_mulai"
                    value="{{ $useOld ? old('jam_mulai', '08:00') : '08:00' }}"
                    class="form-control{{ $errorsAktif->has('jam_mulai') ? ' is-invalid' : '' }}"
                    required>
                @if ($errorsAktif->has('jam_mulai'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('jam_mulai') }}</div>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-3">
            <label for="{{ $fieldId('jam_selesai') }}" class="form-label">
                Jam selesai <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-time-five" aria-hidden="true"></i></span>
                <input
                    type="time"
                    id="{{ $fieldId('jam_selesai') }}"
                    name="jam_selesai"
                    value="{{ $useOld ? old('jam_selesai', '16:00') : '16:00' }}"
                    class="form-control{{ $errorsAktif->has('jam_selesai') ? ' is-invalid' : '' }}"
                    required>
                @if ($errorsAktif->has('jam_selesai'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('jam_selesai') }}</div>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-3">
            <label for="{{ $fieldId('durasi_slot') }}" class="form-label">
                Durasi slot (menit) <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-stopwatch" aria-hidden="true"></i></span>
                <input
                    type="number"
                    id="{{ $fieldId('durasi_slot') }}"
                    name="durasi_slot"
                    value="{{ $useOld ? old('durasi_slot', 120) : 120 }}"
                    class="form-control{{ $errorsAktif->has('durasi_slot') ? ' is-invalid' : '' }}"
                    min="1"
                    max="1440"
                    required>
                @if ($errorsAktif->has('durasi_slot'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('durasi_slot') }}</div>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-3">
            <label for="{{ $fieldId('kapasitas') }}" class="form-label">
                Kapasitas <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base bx bx-group" aria-hidden="true"></i></span>
                <input
                    type="number"
                    id="{{ $fieldId('kapasitas') }}"
                    name="kapasitas"
                    value="{{ $useOld ? old('kapasitas', 2) : 2 }}"
                    class="form-control{{ $errorsAktif->has('kapasitas') ? ' is-invalid' : '' }}"
                    min="1"
                    max="100"
                    required>
                @if ($errorsAktif->has('kapasitas'))
                    <div class="invalid-feedback">{{ $errorsAktif->first('kapasitas') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="alert alert-primary d-flex align-items-center gap-3 mb-4" role="note">
    <i class="icon-base bx bx-info-circle icon-md flex-shrink-0" aria-hidden="true"></i>
    <div>
        Setiap hari dalam rentang akan dibuat slot dari jam mulai sampai sebelum jam selesai.
        Slot yang sudah ada akan dilewati secara otomatis.
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary" id="{{ $fieldId('btn-submit-slot') }}">
        <i class="icon-base bx bx-calendar-plus me-1" aria-hidden="true"></i>Buat Slot
    </button>
    @if ($inModal)
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
    @else
        <a href="{{ route('admin.slot-waktu.index') }}" class="btn btn-outline-secondary">Batal</a>
    @endif
</div>
