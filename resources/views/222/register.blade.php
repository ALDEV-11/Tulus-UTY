@include('222.partials.head', ['pageTitle' => 'Daftar Akun'])

<h4 class="mb-1">Daftar Akun</h4>
<p class="mb-6">Lengkapi data diri Anda untuk mulai menggunakan layanan {{ config('app.name') }}.</p>

<form method="POST" action="{{ route('register') }}" class="mb-6">
    @csrf

    <div class="mb-6">
        <label for="name" class="form-label">Nama Lengkap</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-user" aria-hidden="true"></i></span>
            <input
                id="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nama lengkap Anda"
                maxlength="255"
                autocomplete="name"
                required
                autofocus />

            @error('name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="mb-6">
        <label for="no_hp" class="form-label">Nomor Telepon</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-phone" aria-hidden="true"></i></span>
            <input
                id="no_hp"
                type="tel"
                inputmode="tel"
                class="form-control @error('no_hp') is-invalid @enderror"
                name="no_hp"
                value="{{ old('no_hp') }}"
                placeholder="08xxxxxxxxxx"
                maxlength="20"
                autocomplete="tel"
                required />

            @error('no_hp')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-text">Gunakan format 08xx, 62xx, atau +62xx.</div>
    </div>

    <div class="mb-6">
        <label for="email" class="form-label">Email</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-envelope" aria-hidden="true"></i></span>
            <input
                id="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                maxlength="255"
                autocomplete="email"
                required />

            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password">Password</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-lock-alt" aria-hidden="true"></i></span>
            <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                minlength="8"
                autocomplete="new-password"
                required />
            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide" aria-hidden="true"></i></span>

            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-text">Minimal 8 karakter.</div>
    </div>

    <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password-confirm">Konfirmasi Password</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base bx bx-check-shield" aria-hidden="true"></i></span>
            <input
                id="password-confirm"
                type="password"
                class="form-control"
                name="password_confirmation"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                minlength="8"
                autocomplete="new-password"
                required />
            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide" aria-hidden="true"></i></span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">Daftar</button>
</form>

<p class="text-center mb-0">
    <span>Sudah punya akun?</span>
    <a href="{{ route('login') }}">
        <span>Masuk di sini</span>
    </a>
</p>

@include('222.partials.foot')
