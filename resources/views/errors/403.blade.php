<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}">
</head>
<body>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2">403</h1>
            <h4 class="mb-2 mx-2">Akses ditolak</h4>
            <p class="mb-6 mx-2">Anda tidak memiliki izin untuk membuka halaman ini.</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke beranda</a>
        </div>
    </div>
</body>
</html>
