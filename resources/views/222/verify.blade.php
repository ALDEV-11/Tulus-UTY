@include('222.partials.head', ['pageTitle' => 'Verifikasi Email'])

<h4 class="mb-1">Verifikasi Email Anda</h4>

@if (session('resent'))
    <div class="alert alert-success" role="alert">
        Tautan verifikasi baru telah dikirim ke alamat email Anda.
    </div>
@endif

<p class="mb-6">Sebelum melanjutkan, periksa email Anda untuk tautan verifikasi.</p>

<p class="mb-0">
    Jika Anda tidak menerima email,
    @if (Route::has('verification.resend'))
        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">klik di sini untuk meminta tautan baru</button>.
        </form>
    @else
        silakan hubungi administrator.
    @endif
</p>

@include('222.partials.foot')
