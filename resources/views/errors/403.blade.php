<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}">
    <style>
        :root {
            --suzuki-blue: #0033A0;
            --navy: #0A1F44;
            --sky: #4C7DF0;
            --mist: #E8EEF9;
            --neon-yellow: #C8DC00;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--navy) 0%, var(--suzuki-blue) 58%, var(--sky) 100%);
        }

        .misc-wrapper {
            color: #FFFFFF;
        }

        .misc-wrapper h1 {
            color: var(--neon-yellow);
            font-size: clamp(5rem, 14vw, 9rem);
            font-weight: 800;
        }

        .misc-wrapper h4 {
            color: #FFFFFF;
        }

        .misc-wrapper p {
            color: rgba(255, 255, 255, .8);
        }

        .misc-wrapper .btn-primary {
            --bs-btn-color: var(--navy);
            --bs-btn-bg: var(--neon-yellow);
            --bs-btn-border-color: var(--neon-yellow);
            --bs-btn-hover-color: var(--navy);
            --bs-btn-hover-bg: #B7CB00;
            --bs-btn-hover-border-color: #B7CB00;
        }
    </style>
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
