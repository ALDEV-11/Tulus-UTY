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
            @if ($user && $user->role?->value === 'admin')
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a
                        class="nav-link dropdown-toggle hide-arrow reservation-bell"
                        href="javascript:void(0);"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Notifikasi reservasi">
                        <i class="icon-base bx bx-bell icon-md" aria-hidden="true"></i>
                        <span
                            class="badge rounded-pill bg-danger {{ ($pendingReservasiCount ?? 0) > 0 ? '' : 'd-none' }}"
                            data-reservation-badge>{{ $pendingReservasiCount ?? 0 }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end reservation-notification-menu p-0">
                        <li class="dropdown-header px-3 pt-3 pb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="fw-semibold text-body">Notifikasi Reservasi</span>
                                <span class="badge bg-label-warning" data-reservation-count-label>{{ $pendingReservasiCount ?? 0 }}</span>
                            </div>
                            <small class="text-body-secondary">Menunggu konfirmasi admin</small>
                        </li>
                        <li><div class="dropdown-divider my-0"></div></li>
                        <li>
                            <ul class="list-unstyled reservation-notification-list mb-0" id="reservation-notification-list">
                                @forelse (($recentPendingReservasi ?? collect()) as $reservasi)
                                    <li class="notification-item" data-reservation-id="{{ $reservasi->id }}">
                                        <a class="dropdown-item py-3" href="{{ route('admin.reservasi.index', ['q' => $reservasi->kode]) }}">
                                            <div class="d-flex align-items-start gap-3">
                                                <span class="avatar avatar-sm flex-shrink-0">
                                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                                        <i class="icon-base bx bx-calendar-plus" aria-hidden="true"></i>
                                                    </span>
                                                </span>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold small">{{ $reservasi->pelanggan?->name ?? 'Pelanggan' }}</div>
                                                    <div class="text-body-secondary small">
                                                        {{ $reservasi->kode }} &middot;
                                                        {{ $reservasi->slot?->tanggal?->translatedFormat('d M Y') ?? '-' }}
                                                        {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).'–'.substr($reservasi->slot->jam_selesai, 0, 5) : '' }}
                                                    </div>
                                                    <div class="text-body-secondary small">
                                                        {{ $reservasi->kendaraan?->plat_nomor ?? '-' }}
                                                        {{ $reservasi->kendaraan?->merk?->nama ? '· '.$reservasi->kendaraan->merk->nama : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li id="reservation-notification-empty">
                                        <div class="dropdown-item-text text-center text-body-secondary small py-4">
                                            Belum ada reservasi yang menunggu konfirmasi.
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        <li><div class="dropdown-divider my-0"></div></li>
                        <li>
                            <a class="dropdown-item text-center small fw-semibold py-2" href="{{ route('admin.reservasi.index', ['status' => 'menunggu_konfirmasi']) }}">
                                Lihat semua reservasi
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

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
                                <small class="text-body-secondary">{{ $user->role?->value === 'admin' ? 'Admin' : 'Pelanggan' }}</small>
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
