<!doctype html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-menu-fixed layout-compact"
  data-assets-path="{{ url('dist-dashboard/assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Booking Saya') | {{ config('app.name') }}</title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="{{ asset('dist-dashboard/assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/app-ui.css') }}" />

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    @include('customer.partials.sidebar-style')

    <script src="{{ asset('dist-dashboard/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/config.js') }}"></script>

    @stack('styles')
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        @include('customer.partials.sidebar', [
          'activeMenu' => trim((string) $__env->yieldContent('activeMenu')) ?: ($activeMenu ?? 'booking'),
        ])

        <div class="layout-page">
          @include('customer.partials.navbar')

          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>

            @include('customer.partials.footer')

            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>

      <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/main.js') }}"></script>

    @include('partials.sweetalert')
    @stack('scripts')
  </body>
</html>
