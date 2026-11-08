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

    <link rel="icon" type="image/png" href="{{ asset('images/logo-favicon.png') }}" />

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

    <style>
      :root {
        --bs-primary: #0033A0;
        --bs-primary-rgb: 0, 51, 160;
        --bs-primary-bg-subtle: #E8EEF9;
        --bs-primary-border-subtle: #B7C7E8;
        --bs-link-color: #0033A0;
        --bs-link-hover-color: #0A1F44;
        --bs-warning: #C8DC00;
        --bs-warning-rgb: 200, 220, 0;
        --bs-warning-text-emphasis: #0A1F44;
        --bs-body-bg: #F4F6FA;
      }

      body {
        background-color: #F4F6FA;
      }

      .bg-menu-theme {
        background-color: #0A1F44 !important;
      }

      .bg-menu-theme .menu-link,
      .bg-menu-theme .menu-header-text,
      .bg-menu-theme .app-brand-text,
      .bg-menu-theme .app-brand-logo {
        color: rgba(255, 255, 255, .84) !important;
      }

      .app-brand-image {
        display: block;
        width: 2rem;
        height: 2rem;
        object-fit: contain;
      }

      .bg-menu-theme .app-brand-text {
        font-size: 1rem !important;
        letter-spacing: -.02em;
        white-space: nowrap;
      }

      .bg-menu-theme .menu-item.active > .menu-link {
        color: #FFFFFF !important;
        background-color: #0033A0 !important;
      }

      .menu .app-brand .layout-menu-toggle {
        border: 7px solid #FFFFFF;
        background-color: #FFFFFF;
      }

      .menu .app-brand .layout-menu-toggle i {
        color: #0033A0;
      }

      @media (min-width: 1200px) {
        .menu .app-brand .layout-menu-toggle,
        .layout-menu-hover .menu .app-brand .layout-menu-toggle,
        .menu .app-brand .layout-menu-toggle::before {
          display: none !important;
        }
      }

      .btn-primary {
        --bs-btn-bg: #0033A0;
        --bs-btn-border-color: #0033A0;
        --bs-btn-hover-bg: #0A1F44;
        --bs-btn-hover-border-color: #0A1F44;
        --bs-btn-active-bg: #0A1F44;
        --bs-btn-active-border-color: #0A1F44;
      }

      .btn-warning {
        --bs-btn-color: #0A1F44;
        --bs-btn-bg: #C8DC00;
        --bs-btn-border-color: #C8DC00;
        --bs-btn-hover-color: #0A1F44;
        --bs-btn-hover-bg: #B7CB00;
        --bs-btn-hover-border-color: #B7CB00;
      }

      .bg-label-primary {
        color: #0033A0 !important;
        background-color: #E8EEF9 !important;
      }

      .bg-label-warning {
        color: #0A1F44 !important;
        background-color: #C8DC00 !important;
      }
    </style>

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
    @include('partials.anti-back-cache')
    @stack('scripts')
  </body>
</html>
