@php
    $canBookOnline = ! auth()->check() || auth()->user()->role->value === 'pelanggan';
@endphp

<!doctype html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-wide"
  data-assets-path="{{ url('dist-dashboard/assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Cek Slot Servis | {{ config('app.name') }}</title>

    <meta name="description" content="Cek ketersediaan slot servis motor per tanggal." />

    <link rel="icon" type="image/x-icon" href="{{ asset('dist-dashboard/assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}" />

    <script src="{{ asset('dist-dashboard/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/config.js') }}"></script>
  </head>

  <body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
      <div class="container-xxl">
        <a class="navbar-brand fw-bold" href="{{ route('slot.publik') }}">{{ config('app.name') }}</a>

        <div class="d-flex flex-wrap gap-2">
          @auth
            @if (auth()->user()->role->value === 'pelanggan')
              <a href="{{ route('booking-saya') }}" class="btn btn-sm btn-primary">Booking Saya</a>
            @else
              <a href="{{ route('admin') }}" class="btn btn-sm btn-primary">Dashboard Admin</a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Masuk</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Daftar</a>
            @endif
          @endauth
        </div>
      </div>
    </nav>

    <main class="container-xxl py-6">
      @include('partials.slot-availability')
    </main>

    <footer class="content-footer footer bg-footer-theme">
      <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
          <div class="mb-2 mb-md-0">&copy; {{ now()->year }} {{ config('app.name') }}</div>
          <div class="text-body-secondary small">Cek slot servis tanpa perlu login.</div>
        </div>
      </div>
    </footer>

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/main.js') }}"></script>
  </body>
</html>
