<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed layout-compact"
      data-assets-path="{{ url('dist-dashboard/assets') }}/"
      data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Admin' }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('dist-dashboard/assets/img/favicon/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <style>
        /* ── Admin table ── */
        .admin-table .table > :not(caption) > * > * {
            padding-block: 0.8rem;
        }

        .admin-table .btn-icon {
            min-width: 2.75rem;
            min-height: 2.75rem;
        }

        .admin-table .pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.75rem;
            min-height: 2.75rem;
        }

        .admin-table .table-responsive {
            -webkit-overflow-scrolling: touch;
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

        /* ── Progress bar mini ── */
        .admin-table .progress {
            border-radius: 4px;
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
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="status">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
