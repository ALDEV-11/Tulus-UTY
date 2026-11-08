<!doctype html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-wide customizer-hide"
  data-assets-path="{{ url('dist-dashboard/assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ $pageTitle ?? 'Autentikasi' }} | {{ config('app.name') }}</title>

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
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/pages/page-auth.css') }}" />

    <style>
      :root {
        --bs-primary: #0033A0;
        --bs-primary-rgb: 0, 51, 160;
        --bs-primary-bg-subtle: #E8EEF9;
        --bs-link-color: #0033A0;
        --bs-link-hover-color: #0A1F44;
        --bs-warning: #C8DC00;
        --bs-warning-rgb: 200, 220, 0;
      }

      body {
        background: linear-gradient(135deg, #0A1F44 0%, #0033A0 58%, #4C7DF0 100%);
      }

      .authentication-wrapper.authentication-basic .authentication-inner::before,
      .authentication-wrapper.authentication-basic .authentication-inner::after {
        display: none;
      }

      .card {
        border-top: 0;
      }

      .app-brand-text,
      .text-primary {
        color: #0033A0 !important;
      }

      .auth-logo {
        display: block;
        width: 4.5rem;
        height: 4.5rem;
        object-fit: contain;
        filter: brightness(0) saturate(100%) invert(17%) sepia(97%) saturate(2500%) hue-rotate(210deg) brightness(78%) contrast(110%);
      }

      .btn-primary {
        --bs-btn-bg: #0033A0;
        --bs-btn-border-color: #0033A0;
        --bs-btn-hover-bg: #0A1F44;
        --bs-btn-hover-border-color: #0A1F44;
      }

      .form-control:focus,
      .input-group:focus-within .form-control,
      .input-group:focus-within .input-group-text {
        border-color: #4C7DF0;
        box-shadow: 0 0 0 .15rem rgba(76, 125, 240, .2);
      }

      .input-group-text {
        color: #0033A0;
      }

      .card .icon-base {
        color: #0033A0 !important;
      }

      .auth-home-link {
        color: #0033A0;
        font-size: .875rem;
        font-weight: 600;
        text-decoration: none;
      }

      .auth-home-link:hover {
        color: #0A1F44;
      }

      .auth-card {
        overflow: hidden;
      }

      .auth-visual {
        display: none;
      }

      @media (min-width: 768px) {
        .authentication-wrapper.authentication-basic .authentication-inner {
          max-width: 980px;
          max-inline-size: 980px;
        }

        .auth-card {
          display: grid;
          grid-template-columns: minmax(280px, .92fr) minmax(360px, 1.08fr);
          min-height: 610px;
          border-radius: 1.25rem;
        }

        .auth-visual {
          display: flex;
          min-height: 100%;
          padding: 2rem;
          align-items: flex-start;
          position: relative;
          overflow: hidden;
          color: #FFFFFF;
          background:
            linear-gradient(180deg, rgba(10, 31, 68, .54), rgba(0, 20, 63, .88)),
            url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1000&q=85') center / cover;
        }

        .auth-visual::after {
          content: '';
          position: absolute;
          inset: 0;
          background: linear-gradient(135deg, rgba(0, 51, 160, .32), transparent 55%);
        }

        .auth-visual-logo {
          position: absolute;
          z-index: 1;
          top: 1.75rem;
          left: 1.75rem;
          width: 7rem;
          height: 7rem;
          object-fit: contain;
          filter: brightness(0) invert(1);
        }

        .auth-visual-name {
          position: absolute;
          z-index: 1;
          top: 9.25rem;
          left: 1.75rem;
          color: #FFFFFF;
          font-size: 1.35rem;
          font-weight: 800;
          letter-spacing: -.04em;
        }

        .auth-card > .card-body {
          display: flex;
          flex-direction: column;
          justify-content: center;
          padding: 3.5rem clamp(2rem, 5vw, 4.5rem) !important;
        }

        .auth-card .app-brand {
          justify-content: flex-start !important;
          margin-bottom: 2rem !important;
        }

        .auth-card .auth-logo {
          display: none;
        }

        .auth-card .app-brand-link {
          gap: 0 !important;
        }

        .auth-card .app-brand-text {
          font-size: 1.5rem;
        }
      }
    </style>

    <script src="{{ asset('dist-dashboard/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/config.js') }}"></script>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <div class="card auth-card px-sm-6 px-0">
            <div class="auth-visual" aria-hidden="true">
              <img src="{{ asset('images/logo.png') }}" alt="" class="auth-visual-logo">
              <span class="auth-visual-name">{{ config('app.name') }}</span>
            </div>
            <div class="card-body">
              <div class="app-brand justify-content-center mb-4">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                  <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
                  <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name') }}</span>
                </a>
              </div>
