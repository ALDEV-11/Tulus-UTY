@php
    $user = auth()->user();
@endphp

<nav
    class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar"
    aria-label="Navigasi utama">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)" aria-label="Buka menu">
            <i class="icon-base bx bx-menu icon-md" aria-hidden="true"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end ms-auto" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center">
            @if ($user)
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a
                        class="nav-link dropdown-toggle hide-arrow p-0"
                        href="javascript:void(0);"
                        data-bs-toggle="dropdown"
                        aria-label="Menu pengguna"
                        aria-expanded="false">
                        <span class="avatar navbar-user-avatar">
                            <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-item-text">
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <small class="text-body-secondary">Pelanggan</small>
                            </div>
                        </li>
                        <li><div class="dropdown-divider my-1"></div></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="icon-base bx bx-power-off icon-md me-2" aria-hidden="true"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</nav>
