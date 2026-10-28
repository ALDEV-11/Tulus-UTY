@php($activeMenu = $activeMenu ?? 'booking')

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('booking-saya') }}" class="app-brand-link">
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
        <li class="menu-item {{ $activeMenu === 'booking' ? 'active' : '' }}">
            <a href="{{ route('booking-saya') }}" class="menu-link" @if ($activeMenu === 'booking') aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-calendar-check" aria-hidden="true"></i>
                <div class="text-truncate">Booking Saya</div>
            </a>
        </li>

        <li class="menu-item {{ $activeMenu === 'kendaraan' ? 'active' : '' }}">
            <a href="{{ route('kendaraan.index') }}" class="menu-link" @if ($activeMenu === 'kendaraan') aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-cycling" aria-hidden="true"></i>
                <div class="text-truncate">Kendaraan Saya</div>
            </a>
        </li>

        <li class="menu-item {{ $activeMenu === 'riwayat' ? 'active' : '' }}">
            <a href="{{ route('riwayat-servis.index') }}" class="menu-link" @if ($activeMenu === 'riwayat') aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-history" aria-hidden="true"></i>
                <div class="text-truncate">Riwayat Servis</div>
            </a>
        </li>

        <li class="menu-item {{ $activeMenu === 'cek-slot' ? 'active' : '' }}">
            <a href="{{ route('slot.publik') }}" class="menu-link" @if ($activeMenu === 'cek-slot') aria-current="page" @endif>
                <i class="menu-icon tf-icons bx bx-time-five" aria-hidden="true"></i>
                <div class="text-truncate">Cek Slot</div>
            </a>
        </li>
    </ul>
</aside>
