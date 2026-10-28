<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin') }}" class="app-brand-link">
            <span class="app-brand-logo demo text-primary">
                <i class="icon-base bx bx-wrench icon-md" aria-hidden="true"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto" aria-label="Sembunyikan menu">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle" aria-hidden="true"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('admin') ? 'active' : '' }}">
            <a href="{{ route('admin') }}" class="menu-link" @if (request()->routeIs('admin')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-home-smile" aria-hidden="true"></i>
                <div class="text-truncate">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.reservasi.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reservasi.index') }}" class="menu-link" @if (request()->routeIs('admin.reservasi.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-calendar-check" aria-hidden="true"></i>
                <div class="text-truncate">Reservasi</div>
                <span
                    class="badge rounded-pill bg-warning ms-auto {{ ($pendingReservasiCount ?? 0) > 0 ? '' : 'd-none' }}"
                    data-reservation-badge>{{ $pendingReservasiCount ?? 0 }}</span>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.servis.*') ? 'active' : '' }}">
            <a href="{{ route('admin.servis.index') }}" class="menu-link" @if (request()->routeIs('admin.servis.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-wrench" aria-hidden="true"></i>
                <div class="text-truncate">Servis</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Master</span></li>

        <li class="menu-item {{ request()->routeIs('admin.merk-motor.*') ? 'active' : '' }}">
            <a href="{{ route('admin.merk-motor.index') }}" class="menu-link" @if (request()->routeIs('admin.merk-motor.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-car" aria-hidden="true"></i>
                <div class="text-truncate">Merk Motor</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
            <a href="{{ route('admin.layanan.index') }}" class="menu-link" @if (request()->routeIs('admin.layanan.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-wrench" aria-hidden="true"></i>
                <div class="text-truncate">Layanan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.slot-waktu.*') ? 'active' : '' }}">
            <a href="{{ route('admin.slot-waktu.index') }}" class="menu-link" @if (request()->routeIs('admin.slot-waktu.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-calendar" aria-hidden="true"></i>
                <div class="text-truncate">Slot Waktu</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.pelanggan.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pelanggan.index') }}" class="menu-link" @if (request()->routeIs('admin.pelanggan.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-group" aria-hidden="true"></i>
                <div class="text-truncate">Pelanggan</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Komunikasi</span></li>

        <li class="menu-item {{ request()->routeIs('admin.whatsapp-log.*') ? 'active' : '' }}">
            <a href="{{ route('admin.whatsapp-log.index') }}" class="menu-link" @if (request()->routeIs('admin.whatsapp-log.*')) aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bxl-whatsapp" aria-hidden="true"></i>
                <div class="text-truncate">Log WhatsApp</div>
            </a>
        </li>
    </ul>
</aside>
