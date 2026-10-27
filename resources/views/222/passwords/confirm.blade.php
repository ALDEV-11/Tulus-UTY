@include('222.partials.head', ['pageTitle' => 'Konfirmasi Password'])

<h4 class="mb-1">Konfirmasi Password</h4>
<p class="mb-6">Silakan konfirmasi password Anda sebelum melanjutkan.</p>

<form method="POST" action="{{ route('password.confirm') }}" class="mb-6">
    @csrf

    <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password">Password</label>
        <div class="input-group input-group-merge">
            <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required
                autocomplete="current-password"
                autofocus />
            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
        </div>

        @error('password')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">Konfirmasi</button>
</form>

@if (Route::has('password.request'))
    <p class="text-center mb-0">
        <a href="{{ route('password.request') }}">Lupa password?</a>
    </p>
@endif

@include('222.partials.foot')
