@include('222.partials.head', ['pageTitle' => 'Reset Password'])

<h4 class="mb-1">Atur Ulang Password</h4>
<p class="mb-6">Masukkan password baru untuk akun Anda.</p>

<form method="POST" action="{{ route('password.update') }}" class="mb-6">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}" />

    <div class="mb-6">
        <label for="email" class="form-label">Email</label>
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            value="{{ $email ?? old('email') }}"
            required
            autocomplete="email"
            autofocus />

        @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password">Password Baru</label>
        <div class="input-group input-group-merge">
            <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required
                autocomplete="new-password" />
            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
        </div>

        @error('password')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password-confirm">Konfirmasi Password Baru</label>
        <div class="input-group input-group-merge">
            <input
                id="password-confirm"
                type="password"
                class="form-control"
                name="password_confirmation"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required
                autocomplete="new-password" />
            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">Simpan Password Baru</button>
</form>

<p class="text-center mb-0">
    <a href="{{ route('login') }}" class="d-flex justify-content-center">
        <i class="icon-base bx bx-chevron-left icon-20px me-1"></i>
        Kembali ke halaman login
    </a>
</p>

@include('222.partials.foot')
