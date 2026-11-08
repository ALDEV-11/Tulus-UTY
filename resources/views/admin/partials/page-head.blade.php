<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed layout-compact"
      data-assets-path="{{ url('dist-dashboard/assets') }}/"
      data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="broadcast-auth-endpoint" content="{{ url('broadcasting/auth') }}">
    <title>{{ $pageTitle ?? 'Admin' }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/app-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    @vite('resources/js/app.js')
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

        /* ── Page header ── */
        @media (max-width: 575.98px) {
            .admin-page-header {
                align-items: stretch !important;
            }

            .admin-page-header .btn {
                width: 100%;
            }
        }

        /* ── Empty state ── */
        .py-8 {
            padding-block: 3rem !important;
        }

        /* ── Form control plaintext dalam input-group ── */
        .input-group > p.form-control {
            background-color: var(--bs-secondary-bg);
            color: var(--bs-body-color);
            cursor: default;
        }

        /* ── Card section header ── */
        .avatar.avatar-xs {
            width: 1.5rem;
            height: 1.5rem;
            min-width: 1.5rem;
        }

        .avatar.avatar-xs .avatar-initial {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .navbar-user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--bs-primary);
            color: #fff;
        }

        .navbar-user-avatar .avatar-initial {
            width: 100%;
            height: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: transparent !important;
            color: #fff !important;
            font-weight: 600;
            line-height: 1;
        }

        /* ── Sidebar tanpa scrollbar ── */
        .layout-menu .menu-inner {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .layout-menu .menu-inner::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .layout-menu .ps__rail-x,
        .layout-menu .ps__rail-y {
            display: none !important;
        }
    </style>
    @stack('styles')
    <script src="{{ asset('dist-dashboard/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/js/config.js') }}"></script>
</head>
<body>
<a class="visually-hidden-focusable position-absolute top-0 start-0 p-3 bg-body text-body z-3" href="#main-content">Lewati ke konten</a>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @include('admin.partials.sidebar')
        <div class="layout-page">
            @include('admin.partials.navbar')
            <div class="content-wrapper">
                <main id="main-content" class="container-xxl flex-grow-1 container-p-y">
