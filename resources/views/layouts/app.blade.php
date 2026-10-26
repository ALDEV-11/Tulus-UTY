<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('dist-dashboard/assets/img/favicon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/pages/page-auth.css') }}">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (Auth::user()->role === \App\Enums\UserRole::Admin)
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin') }}">Admin</a></li>
                            @elseif (Auth::user()->role === \App\Enums\UserRole::Pelanggan)
                                <li class="nav-item"><a class="nav-link" href="{{ route('booking-saya') }}">Booking Saya</a></li>
                            @endif
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @auth
            <aside class="border-bottom bg-light">
                <div class="container py-2 d-flex gap-3">
                    @if (Auth::user()->role === \App\Enums\UserRole::Admin)
                        <a href="{{ route('admin') }}" class="text-decoration-none">Dashboard Admin</a>
                    @elseif (Auth::user()->role === \App\Enums\UserRole::Pelanggan)
                        <a href="{{ route('booking-saya') }}" class="text-decoration-none">Booking Saya</a>
                    @endif
                </div>
            </aside>
        @endauth

        <main class="py-4">@yield('content')</main>
    </div>

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
