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
        <label for="{{ $fieldId('name') }}" class="form-label">
            Nama pelanggan <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-user" aria-hidden="true"></i></span>
            <input
                type="text"
                id="{{ $fieldId('name') }}"
                name="name"
                value="{{ $useOld ? old('name', $pelanggan->name) : $pelanggan->name }}"
                class="form-control{{ $errorsAktif->has('name') ? ' is-invalid' : '' }}"
                maxlength="100"
                placeholder="Nama lengkap pelanggan"
                required
                @if (! $inModal) autofocus @endif>
            @if ($errorsAktif->has('name'))
                <div class="invalid-feedback">{{ $errorsAktif->first('name') }}</div>
            @endif
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $fieldId('email') }}" class="form-label">
            Email <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-envelope" aria-hidden="true"></i></span>
            <input
                type="email"
                id="{{ $fieldId('email') }}"
                name="email"
                value="{{ $useOld ? old('email', $pelanggan->email) : $pelanggan->email }}"
                class="form-control{{ $errorsAktif->has('email') ? ' is-invalid' : '' }}"
                maxlength="150"
                placeholder="nama@email.com"
                required>
            @if ($errorsAktif->has('email'))
                <div class="invalid-feedback">{{ $errorsAktif->first('email') }}</div>
            @endif
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $fieldId('no_hp') }}" class="form-label">Nomor HP</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-phone" aria-hidden="true"></i></span>
            <input
                type="text"
                id="{{ $fieldId('no_hp') }}"
                name="no_hp"
                value="{{ $useOld ? old('no_hp', $pelanggan->no_hp) : $pelanggan->no_hp }}"
                class="form-control{{ $errorsAktif->has('no_hp') ? ' is-invalid' : '' }}"
                maxlength="20"
                placeholder="62812xxxxxxx">
            @if ($errorsAktif->has('no_hp'))
                <div class="invalid-feedback">{{ $errorsAktif->first('no_hp') }}</div>
            @endif
        </div>
        <div class="form-text">Gunakan format internasional, mis. 6281234567890.</div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $fieldId('password') }}" class="form-label">
            Password @if (! $pelanggan->exists)<span class="text-danger" aria-hidden="true">*</span>@endif
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-lock-alt" aria-hidden="true"></i></span>
            <input
                type="password"
                id="{{ $fieldId('password') }}"
                name="password"
                class="form-control{{ $errorsAktif->has('password') ? ' is-invalid' : '' }}"
                minlength="8"
                autocomplete="new-password"
                @if (! $pelanggan->exists) required @endif>
            @if ($errorsAktif->has('password'))
                <div class="invalid-feedback">{{ $errorsAktif->first('password') }}</div>
            @endif
        </div>
        <div class="form-text">
            @if ($pelanggan->exists)
                Kosongkan jika password tidak ingin diubah.
            @else
                Minimal 8 karakter.
            @endif
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="{{ $fieldId('password_confirmation') }}" class="form-label">
            Konfirmasi password @if (! $pelanggan->exists)<span class="text-danger" aria-hidden="true">*</span>@endif
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-check-shield" aria-hidden="true"></i></span>
            <input
                type="password"
                id="{{ $fieldId('password_confirmation') }}"
                name="password_confirmation"
                class="form-control"
                minlength="8"
                autocomplete="new-password"
                @if (! $pelanggan->exists) required @endif>
        </div>
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
                    @checked($useOld ? old('aktif', $pelanggan->aktif) : $pelanggan->aktif)>
                <label class="form-check-label" for="{{ $fieldId('aktif') }}">
                    <span class="fw-semibold">Akun aktif</span>
                    <span class="text-body-secondary d-block small">Pelanggan dapat login dan membuat reservasi</span>
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
        <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-outline-secondary">Batal</a>
    @endif
</div>
