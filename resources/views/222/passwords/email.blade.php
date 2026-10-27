@include('222.partials.head', ['pageTitle' => 'Lupa Password'])

<h4 class="mb-1">Lupa Password?</h4>
<p class="mb-6">Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang password.</p>

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="mb-6">
    @csrf

    <div class="mb-6">
        <label for="email" class="form-label">Email</label>
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            autofocus />

        @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">Kirim Tautan Reset</button>
</form>

<p class="text-center mb-0">
    <a href="{{ route('login') }}" class="d-flex justify-content-center">
        <i class="icon-base bx bx-chevron-left icon-20px me-1"></i>
        Kembali ke halaman login
    </a>
</p>

@include('222.partials.foot')
