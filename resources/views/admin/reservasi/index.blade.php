@php
    $pageTitle = 'Reservasi';
    $filterActive = $search !== '' || $status || $dari || $sampai;
    $statusIcons = [
        'menunggu_konfirmasi' => 'bx bx-time-five',
        'dikonfirmasi' => 'bx bx-check-circle',
        'check_in' => 'bx bx-log-in-circle',
        'selesai' => 'bx bx-flag',
        'dibatalkan' => 'bx bx-x-circle',
    ];
    $statusColors = [
        'menunggu_konfirmasi' => 'warning',
        'dikonfirmasi' => 'primary',
        'check_in' => 'info',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];
    $statusServisLabels = $statusServisLabels ?? [
        'menunggu' => 'Menunggu',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'diambil' => 'Diambil',
    ];
    $statusServisBadges = $statusServisBadges ?? [
        'menunggu' => 'warning',
        'dikerjakan' => 'info',
        'selesai' => 'success',
        'diambil' => 'primary',
    ];
    $whatsappStatusLabels = [
        'menunggu' => 'Menunggu',
        'terkirim' => 'Terkirim',
        'gagal' => 'Gagal',
    ];
    $whatsappStatusBadges = [
        'menunggu' => 'warning',
        'terkirim' => 'success',
        'gagal' => 'danger',
    ];
@endphp

@push('styles')
<style>
    .reservation-status-card {
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .reservation-status-card:hover,
    .reservation-status-card:focus-visible {
        border-color: var(--bs-primary);
        box-shadow: 0 .25rem .75rem rgba(105, 108, 255, .18);
        transform: translateY(-1px);
        outline: 0;
    }

    .reservation-status-card.is-active {
        border-color: var(--bs-primary);
        box-shadow: inset 0 0 0 1px var(--bs-primary);
    }

    .reservation-detail-label {
        color: var(--bs-secondary-color);
        font-size: .75rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .reservation-detail-value {
        font-weight: 600;
    }

    .reservation-log-message {
        min-width: 16rem;
        white-space: normal;
    }

    @media (prefers-reduced-motion: reduce) {
        .reservation-status-card {
            transition: none;
        }
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Reservasi',
    'subtitle' => $subtitle,
    'icon' => 'bx bx-calendar-check',
])

@if (! $pusherConfigured)
    <div class="alert alert-info d-flex align-items-start gap-2" role="status">
        <i class="icon-base bx bx-info-circle mt-1" aria-hidden="true"></i>
        <div>
            <strong>Notifikasi real-time belum aktif.</strong>
            Halaman ini memakai polling setiap 30 detik. Isi kredensial Pusher di <code>.env</code>
            lalu ubah <code>BROADCAST_CONNECTION=pusher</code> untuk mengaktifkan notifikasi instan.
        </div>
    </div>
@else
    <div class="alert alert-success d-flex align-items-center gap-2" role="status">
        <i class="icon-base bx bx-broadcast" aria-hidden="true"></i>
        <span>Notifikasi real-time Pusher aktif untuk reservasi baru.</span>
    </div>
@endif

<div class="row g-3 mb-4">
    @foreach ($statusLabels as $statusValue => $statusLabel)
        @php
            $isActive = $status === $statusValue;
            $filterUrl = route('admin.reservasi.index', array_filter(
                ['status' => $statusValue],
                fn ($value) => $value !== null && $value !== ''
            ));
        @endphp
        <div class="col-6 col-xl-3">
            <a href="{{ $filterUrl }}"
               class="card h-100 text-decoration-none reservation-status-card {{ $isActive ? 'is-active' : '' }}"
               @if ($isActive) aria-current="true" @endif>
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <p class="text-body-secondary mb-1">{{ $statusLabel }}</p>
                            <h3 class="mb-0">{{ number_format($statusCounts[$statusValue] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <span class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-{{ $statusColors[$statusValue] ?? 'secondary' }}">
                                <i class="icon-base {{ $statusIcons[$statusValue] ?? 'bx bx-calendar' }}" aria-hidden="true"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card admin-table">
    <div class="card-header py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Daftar Reservasi</h5>
                    <small class="text-body-secondary">Konfirmasi, pantau, dan batalkan reservasi pelanggan</small>
                </div>
            </div>
            @if ($filterActive)
                <a href="{{ route('admin.reservasi.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset Filter
                </a>
            @endif
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="GET" action="{{ route('admin.reservasi.index') }}" class="row g-2 align-items-end" role="search">
            <div class="col-12 col-lg-4">
                <label class="form-label" for="q">Cari reservasi</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                    <input
                        type="search"
                        class="form-control"
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Kode, nama, nomor HP, atau plat nomor"
                        maxlength="100"
                        autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua status</option>
                    @foreach ($statusLabels as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" for="dari">Dari tanggal</label>
                <input type="date" class="form-control" id="dari" name="dari" value="{{ $dari }}">
            </div>
            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" for="sampai">Sampai tanggal</label>
                <input type="date" class="form-control" id="sampai" name="sampai" value="{{ $sampai }}">
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="icon-base bx bx-filter-alt me-1" aria-hidden="true"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <caption class="visually-hidden">Daftar reservasi beserta pelanggan, kendaraan, layanan, jadwal, dan statusnya</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">Kode</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Kendaraan</th>
                    <th scope="col" class="d-none d-lg-table-cell">Layanan</th>
                    <th scope="col">Jadwal</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($reservasis as $reservasi)
                    @php
                        $statusValue = $reservasi->status->value;
                        $layananLabel = $reservasi->layanan->pluck('nama')->join(', ');
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $reservasi->kode }}</span><br>
                            <small class="text-body-secondary">{{ $reservasi->created_at?->translatedFormat('d M Y H:i') }}</small>
                        </td>
                        <td>
                            {{ $reservasi->pelanggan?->name ?? '-' }}<br>
                            <small class="text-body-secondary">{{ $reservasi->pelanggan?->no_hp ?: 'Nomor HP belum diisi' }}</small>
                        </td>
                        <td>
                            {{ $reservasi->kendaraan?->plat_nomor ?? '-' }}<br>
                            <small class="text-body-secondary">
                                {{ trim(($reservasi->kendaraan?->merk?->nama ?? '').' '.($reservasi->kendaraan?->model ?? '')) ?: '-' }}
                            </small>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="d-inline-block text-truncate" style="max-width: 14rem" title="{{ $layananLabel ?: 'Belum ada layanan' }}">
                                {{ $layananLabel ?: 'Belum ada layanan' }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            {{ $reservasi->slot?->tanggal?->translatedFormat('d M Y') ?? '-' }}<br>
                            <small class="text-body-secondary">
                                {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).' – '.substr($reservasi->slot->jam_selesai, 0, 5) : '-' }}
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-{{ $statusBadges[$statusValue] ?? 'secondary' }}">
                                {{ $statusLabels[$statusValue] ?? $statusValue }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button
                                type="button"
                                class="btn btn-sm btn-icon btn-text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetailReservasi{{ $reservasi->id }}"
                                title="Lihat detail {{ $reservasi->kode }}"
                                aria-label="Lihat detail {{ $reservasi->kode }}">
                                <i class="icon-base bx bx-show" aria-hidden="true"></i>
                            </button>

                            @if ($statusValue === 'menunggu_konfirmasi')
                                <form
                                    method="POST"
                                    action="{{ route('admin.reservasi.konfirmasi', $reservasi) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Konfirmasi reservasi {{ $reservasi->kode }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-icon btn-text-success"
                                        title="Konfirmasi {{ $reservasi->kode }}"
                                        aria-label="Konfirmasi {{ $reservasi->kode }}">
                                        <i class="icon-base bx bx-check" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @endif

                            @if (in_array($statusValue, ['menunggu_konfirmasi', 'dikonfirmasi'], true))
                                <button
                                    type="button"
                                    class="btn btn-sm btn-icon btn-text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBatalkanReservasi"
                                    data-action="{{ route('admin.reservasi.batalkan', $reservasi) }}"
                                    data-kode="{{ $reservasi->kode }}"
                                    data-mode="{{ $statusValue === 'menunggu_konfirmasi' ? 'reject' : 'cancel' }}"
                                    title="{{ $statusValue === 'menunggu_konfirmasi' ? 'Tolak' : 'Batalkan' }} {{ $reservasi->kode }}"
                                    aria-label="{{ $statusValue === 'menunggu_konfirmasi' ? 'Tolak' : 'Batalkan' }} {{ $reservasi->kode }}">
                                    <i class="icon-base bx bx-x-circle" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-8">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="icon-base bx bx-calendar-x icon-lg" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">{{ $filterActive ? 'Tidak ada reservasi yang cocok' : 'Belum ada reservasi' }}</h6>
                                <p class="text-body-secondary mb-0">
                                    {{ $filterActive ? 'Coba ubah kata kunci atau filter tanggal.' : 'Reservasi dari pelanggan akan muncul di sini.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($reservasis->total() > 0)
        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-body-secondary mb-0">
                Menampilkan {{ $reservasis->firstItem() }} – {{ $reservasis->lastItem() }} dari {{ $reservasis->total() }} reservasi
            </p>
            @if ($reservasis->hasPages())
                {{ $reservasis->links() }}
            @endif
        </div>
    @endif
</div>

@foreach ($reservasis as $reservasi)
    @php
        $statusValue = $reservasi->status->value;
        $layananLabel = $reservasi->layanan->pluck('nama')->join(', ');
        $servis = $reservasi->servis;
        $servisStatusValue = $servis?->status?->value;
        $canCheckIn = $statusValue === 'dikonfirmasi' && ! $servis;
        $canSendCompleted = $servisStatusValue !== null
            && in_array($servisStatusValue, ['selesai', 'diambil'], true);
        $canSendReminder = $statusValue === 'selesai' && $canSendCompleted;
        $nomorWhatsapp = $reservasi->pelanggan?->no_hp;
        $nomorWhatsappValid = \App\Support\WhatsappLink::normalize($nomorWhatsapp) !== null;
        $pernahDitolak = $reservasi->whatsappLogs->contains(
            fn ($log) => $log->jenis?->value === 'penolakan_reservasi'
        );
        $waActions = [];

        if ($statusValue === 'dikonfirmasi') {
            $waActions[] = [
                'jenis' => 'konfirmasi_reservasi',
                'label' => 'Kirim WA Konfirmasi',
                'class' => 'btn-outline-primary',
            ];
        }

        if ($statusValue === 'dibatalkan' && $pernahDitolak) {
            $waActions[] = [
                'jenis' => 'penolakan_reservasi',
                'label' => 'Kirim WA Penolakan',
                'class' => 'btn-outline-danger',
            ];
        }

        if ($canSendCompleted) {
            $waActions[] = [
                'jenis' => 'servis_selesai',
                'label' => 'Kirim WA Servis Selesai',
                'class' => 'btn-outline-success',
            ];
        }

        if ($canSendReminder) {
            $waActions[] = [
                'jenis' => 'pengingat_servis',
                'label' => 'Kirim WA Pengingat',
                'class' => 'btn-outline-success',
            ];
        }
    @endphp
    <div class="modal fade" id="modalDetailReservasi{{ $reservasi->id }}" tabindex="-1" aria-labelledby="modalDetailReservasi{{ $reservasi->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalDetailReservasi{{ $reservasi->id }}Label">Detail Reservasi</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold">{{ $reservasi->kode }}</span>
                            <span class="badge bg-label-{{ $statusBadges[$statusValue] ?? 'secondary' }}">
                                {{ $statusLabels[$statusValue] ?? $statusValue }}
                            </span>
                            @if ($servis)
                                <span class="badge bg-label-{{ $statusServisBadges[$servisStatusValue] ?? 'secondary' }}">
                                    Servis: {{ $statusServisLabels[$servisStatusValue] ?? $servisStatusValue }}
                                </span>
                            @endif
                            <small class="text-body-secondary">Dibuat {{ $reservasi->created_at?->translatedFormat('d M Y H:i') }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @error('reservasi')
                        <div class="alert alert-danger py-2 px-3 small" role="alert">{{ $message }}</div>
                    @enderror

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-user me-2 text-primary" aria-hidden="true"></i>Pelanggan</h6>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Nama</div>
                                    <div class="reservation-detail-value">{{ $reservasi->pelanggan?->name ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Nomor HP</div>
                                    <div class="reservation-detail-value">{{ $reservasi->pelanggan?->no_hp ?: '-' }}</div>
                                </div>
                                <div>
                                    <div class="reservation-detail-label">Email</div>
                                    <div class="reservation-detail-value">{{ $reservasi->pelanggan?->email ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-car me-2 text-primary" aria-hidden="true"></i>Kendaraan</h6>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Plat nomor</div>
                                    <div class="reservation-detail-value">{{ $reservasi->kendaraan?->plat_nomor ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Merk / Model</div>
                                    <div class="reservation-detail-value">
                                        {{ trim(($reservasi->kendaraan?->merk?->nama ?? '').' '.($reservasi->kendaraan?->model ?? '')) ?: '-' }}
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="reservation-detail-label">Tahun</div>
                                        <div class="reservation-detail-value">{{ $reservasi->kendaraan?->tahun ?: '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="reservation-detail-label">Kilometer</div>
                                        <div class="reservation-detail-value">
                                            {{ $reservasi->kendaraan?->kilometer ? number_format($reservasi->kendaraan->kilometer, 0, ',', '.').' km' : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-calendar me-2 text-primary" aria-hidden="true"></i>Jadwal</h6>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Tanggal</div>
                                    <div class="reservation-detail-value">{{ $reservasi->slot?->tanggal?->translatedFormat('l, d M Y') ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="reservation-detail-label">Jam</div>
                                    <div class="reservation-detail-value">
                                        {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).' – '.substr($reservasi->slot->jam_selesai, 0, 5) : '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="reservation-detail-label">Kapasitas slot</div>
                                    <div class="reservation-detail-value">{{ $reservasi->slot?->kapasitas ?? '-' }} kendaraan</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-wrench me-2 text-primary" aria-hidden="true"></i>Layanan</h6>
                                @forelse ($reservasi->layanan as $layanan)
                                    <div class="d-flex justify-content-between gap-3 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                        <span>{{ $layanan->nama }}</span>
                                        <small class="text-body-secondary text-nowrap">{{ $layanan->durasi_menit }} menit</small>
                                    </div>
                                @empty
                                    <p class="text-body-secondary mb-0">Belum ada layanan dipilih.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2"><i class="icon-base bx bx-message-square-detail me-2 text-primary" aria-hidden="true"></i>Keluhan Pelanggan</h6>
                                <p class="mb-0 text-break">{{ $reservasi->keluhan ?: 'Tidak ada keluhan yang dicatat.' }}</p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-2"><i class="icon-base bx bx-note me-2 text-primary" aria-hidden="true"></i>Catatan Admin</h6>
                                <p class="mb-0 text-break" style="white-space: pre-line">{{ $reservasi->catatan ?: 'Belum ada catatan admin.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mt-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2 mb-3">
                            <div>
                                <h6 class="mb-1"><i class="icon-base bx bx-wrench me-2 text-primary" aria-hidden="true"></i>Servis</h6>
                                <small class="text-body-secondary">
                                    @if ($servis)
                                        Progres pengerjaan dan catatan servis untuk reservasi ini.
                                    @else
                                        Check in kendaraan saat pelanggan datang untuk membuat data servis.
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                @if ($servis)
                                    <span class="badge bg-label-{{ $statusServisBadges[$servisStatusValue] ?? 'secondary' }}">
                                        {{ $statusServisLabels[$servisStatusValue] ?? $servisStatusValue }}
                                    </span>
                                    <a href="{{ route('admin.servis.index', ['q' => $reservasi->kode]) }}" class="btn btn-sm btn-outline-primary">
                                        Kelola Servis
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($servis)
                            @include('partials.service-timeline', ['servis' => $servis])

                            <div class="row g-3 mt-1">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="reservation-detail-label">Tanggal masuk</div>
                                    <div class="reservation-detail-value">{{ $servis->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="reservation-detail-label">Tanggal selesai</div>
                                    <div class="reservation-detail-value">{{ $servis->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="reservation-detail-label">Mulai dikerjakan</div>
                                    <div class="reservation-detail-value">{{ $servis->waktu_mulai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="reservation-detail-label">Selesai dikerjakan</div>
                                    <div class="reservation-detail-value">{{ $servis->waktu_selesai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="reservation-detail-label">Odometer awal</div>
                                    <div class="reservation-detail-value">
                                        {{ $servis->odometer_awal !== null ? number_format($servis->odometer_awal, 0, ',', '.').' km' : '-' }}
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="reservation-detail-label">Odometer akhir</div>
                                    <div class="reservation-detail-value">
                                        {{ $servis->odometer !== null ? number_format($servis->odometer, 0, ',', '.').' km' : '-' }}
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="reservation-detail-label">Rincian pekerjaan</div>
                                    <div class="text-break" style="white-space: pre-line">{{ $servis->rincian_pekerjaan ?: '-' }}</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="reservation-detail-label">Catatan servis</div>
                                    <div class="text-break" style="white-space: pre-line">{{ $servis->catatan ?: '-' }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($canCheckIn)
                            <hr class="my-4">
                            <h6 class="mb-1">Check In Kendaraan</h6>
                            <p class="small text-body-secondary mb-3">
                                Masukkan odometer saat kendaraan masuk. Data servis akan dibuat dengan status
                                <strong>Menunggu</strong> dan reservasi berubah menjadi <strong>Check In</strong>.
                            </p>

                            <form
                                method="POST"
                                id="form-check-in-{{ $reservasi->id }}"
                                action="{{ route('admin.reservasi.check-in', $reservasi) }}"
                                onsubmit="return confirm('Check in kendaraan untuk reservasi {{ $reservasi->kode }}?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_modal" value="modalDetailReservasi{{ $reservasi->id }}">

                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label" for="odometer-awal-{{ $reservasi->id }}">Odometer awal (km)</label>
                                        <input
                                            type="number"
                                            class="form-control @error('odometer_awal') is-invalid @enderror"
                                            id="odometer-awal-{{ $reservasi->id }}"
                                            name="odometer_awal"
                                            min="0"
                                            max="9999999"
                                            step="1"
                                            value="{{ old('odometer_awal', $reservasi->kendaraan?->odometer_terakhir ?: $reservasi->kendaraan?->kilometer) }}"
                                            inputmode="numeric"
                                            required>
                                        <div class="form-text">
                                            Odometer terakhir tercatat:
                                            {{ $reservasi->kendaraan?->odometer_terakhir !== null
                                                ? number_format($reservasi->kendaraan->odometer_terakhir, 0, ',', '.').' km'
                                                : '-' }}
                                        </div>
                                        @error('odometer_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        @elseif ($statusValue === 'menunggu_konfirmasi')
                            <p class="small text-body-secondary mb-0">
                                Konfirmasi reservasi terlebih dahulu sebelum melakukan check in kendaraan.
                            </p>
                        @elseif ($statusValue === 'dikonfirmasi' && $servis)
                            <p class="small text-body-secondary mb-0">
                                Reservasi ini sudah memiliki data servis. Kelola status pengerjaan melalui menu Servis.
                            </p>
                        @elseif ($statusValue === 'check_in' && $servis)
                            <p class="small text-body-secondary mb-0">
                                Kendaraan sudah check in. Perbarui catatan dan status servis melalui menu Servis.
                            </p>
                        @elseif ($statusValue === 'selesai')
                            <p class="small text-body-secondary mb-0">
                                Servis sudah diambil dan reservasi telah selesai.
                            </p>
                        @elseif ($statusValue === 'dibatalkan')
                            <p class="small text-body-secondary mb-0">
                                Reservasi dibatalkan sehingga tidak diproses sebagai servis.
                            </p>
                        @endif
                    </div>

                    @if (count($waActions) > 0)
                        <div class="border rounded p-3 mt-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2 mb-3">
                                <div>
                                    <h6 class="mb-1"><i class="icon-base bxl-whatsapp me-2 text-success" aria-hidden="true"></i>Kirim WhatsApp</h6>
                                    <small class="text-body-secondary">Pesan dicatat ke log lalu dibuka di tab baru.</small>
                                </div>
                                <span class="badge bg-label-{{ $nomorWhatsappValid ? 'success' : 'danger' }}">
                                    {{ $nomorWhatsappValid ? $nomorWhatsapp : 'Nomor HP tidak valid' }}
                                </span>
                            </div>

                            @if (! $nomorWhatsappValid)
                                <div class="alert alert-warning py-2 px-3 small mb-0" role="alert">
                                    Nomor HP pelanggan belum diisi atau tidak valid. Perbarui data pelanggan terlebih dahulu sebelum mengirim WhatsApp.
                                </div>
                            @else
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($waActions as $action)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.reservasi.kirim-wa', $reservasi) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="d-inline"
                                            id="form-kirim-wa-{{ $reservasi->id }}-{{ $action['jenis'] }}">
                                            @csrf
                                            <input type="hidden" name="jenis" value="{{ $action['jenis'] }}">
                                            <input type="hidden" name="_modal" value="modalDetailReservasi{{ $reservasi->id }}">
                                            <button type="submit" class="btn btn-sm {{ $action['class'] }}">
                                                <i class="icon-base bxl-whatsapp me-1" aria-hidden="true"></i>{{ $action['label'] }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                                <div class="form-text mt-2">Log dibuat dengan status menunggu. Muat ulang halaman untuk melihat log terbaru.</div>
                            @endif

                            @error('whatsapp')
                                <div class="alert alert-danger py-2 px-3 small mt-3 mb-0" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="border rounded p-3 mt-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                            <div>
                                <h6 class="mb-1"><i class="icon-base bxl-whatsapp me-2 text-success" aria-hidden="true"></i>Log WhatsApp</h6>
                                <small class="text-body-secondary">Pesan otomatis dan manual untuk reservasi ini</small>
                            </div>
                            <a href="{{ route('admin.whatsapp-log.index', ['q' => $reservasi->kode]) }}" class="btn btn-sm btn-outline-success">
                                Lihat semua log
                            </a>
                        </div>

                        @if ($reservasi->whatsappLogs->isEmpty())
                            <p class="text-body-secondary mb-0">Belum ada log WhatsApp untuk reservasi ini.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <caption class="visually-hidden">Log WhatsApp untuk reservasi {{ $reservasi->kode }}</caption>
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Waktu</th>
                                            <th scope="col">Jenis</th>
                                            <th scope="col">Tujuan</th>
                                            <th scope="col">Pesan</th>
                                            <th scope="col" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reservasi->whatsappLogs->sortByDesc('created_at') as $log)
                                            <tr>
                                                <td class="text-nowrap small">{{ $log->created_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                                                <td class="small">{{ $log->jenis?->label() ?? '-' }}</td>
                                                <td class="small text-nowrap">{{ $log->nomor_tujuan }}</td>
                                                <td class="small reservation-log-message" title="{{ $log->pesan }}">
                                                    {{ \Illuminate\Support\Str::limit($log->pesan, 120) }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-{{ $whatsappStatusBadges[$log->status] ?? 'secondary' }}">
                                                        {{ $whatsappStatusLabels[$log->status] ?? $log->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    @if (in_array($statusValue, ['menunggu_konfirmasi', 'dikonfirmasi'], true))
                        @php $isRejection = $statusValue === 'menunggu_konfirmasi'; @endphp
                        <form
                            method="POST"
                            action="{{ route('admin.reservasi.batalkan', $reservasi) }}"
                            class="border border-danger-subtle rounded p-3 mt-4"
                            onsubmit="return confirm('{{ $isRejection ? 'Tolak' : 'Batalkan' }} reservasi {{ $reservasi->kode }}?')">
                            @csrf
                            @method('PATCH')
                            <h6 class="mb-1 text-danger">{{ $isRejection ? 'Tolak Reservasi' : 'Batalkan Reservasi' }}</h6>
                            <p class="small text-body-secondary mb-3">
                                {{ $isRejection
                                    ? 'Reservasi akan ditolak dan pelanggan menerima log WhatsApp penolakan.'
                                    : 'Pelanggan akan menerima log WhatsApp pembatalan.' }}
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <label class="visually-hidden" for="alasan-reservasi-{{ $reservasi->id }}">Alasan {{ $isRejection ? 'penolakan' : 'pembatalan' }}</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="alasan-reservasi-{{ $reservasi->id }}"
                                    name="alasan"
                                    maxlength="500"
                                    @if ($isRejection) required @endif
                                    placeholder="{{ $isRejection ? 'Alasan penolakan (wajib)' : 'Alasan pembatalan (opsional)' }}">
                                <button type="submit" class="btn btn-outline-danger text-nowrap">{{ $isRejection ? 'Tolak' : 'Batalkan' }}</button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    @if ($statusValue === 'menunggu_konfirmasi')
                        <form
                            method="POST"
                            action="{{ route('admin.reservasi.konfirmasi', $reservasi) }}"
                            onsubmit="return confirm('Konfirmasi reservasi {{ $reservasi->kode }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base bx bx-check me-1" aria-hidden="true"></i>Konfirmasi Reservasi
                            </button>
                        </form>
                    @endif
                    @if ($canCheckIn)
                        <button
                            type="submit"
                            form="form-check-in-{{ $reservasi->id }}"
                            class="btn btn-primary">
                            <i class="icon-base bx bx-log-in-circle me-1" aria-hidden="true"></i>Check In Kendaraan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

<div class="modal fade" id="modalBatalkanReservasi" tabindex="-1" aria-labelledby="modalBatalkanReservasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="form-batalkan-reservasi">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalBatalkanReservasiLabel">Batalkan Reservasi</h5>
                        <small class="text-body-secondary" id="batalkan-reservasi-kode"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <p class="text-body-secondary" id="batalkan-reservasi-deskripsi">
                        Reservasi akan dibatalkan dan pelanggan menerima log WhatsApp pembatalan.
                    </p>
                    <label for="alasan-batalkan" class="form-label" id="batalkan-reservasi-alasan-label">Alasan pembatalan (opsional)</label>
                    <textarea class="form-control" id="alasan-batalkan" name="alasan" rows="3" maxlength="500" placeholder="Contoh: pelanggan meminta jadwal ulang"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger" id="batalkan-reservasi-submit">Batalkan Reservasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalBatalkanReservasi');

        if (!modal) {
            return;
        }

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const form = document.getElementById('form-batalkan-reservasi');
            const kode = document.getElementById('batalkan-reservasi-kode');
            const alasan = document.getElementById('alasan-batalkan');
            const title = document.getElementById('modalBatalkanReservasiLabel');
            const description = document.getElementById('batalkan-reservasi-deskripsi');
            const reasonLabel = document.getElementById('batalkan-reservasi-alasan-label');
            const submit = document.getElementById('batalkan-reservasi-submit');

            if (!button || !form) {
                return;
            }

            const isReject = button.getAttribute('data-mode') === 'reject';

            form.action = button.getAttribute('data-action') || '';
            kode.textContent = button.getAttribute('data-kode') || '';
            alasan.value = '';
            alasan.required = isReject;
            alasan.placeholder = isReject
                ? 'Contoh: jadwal penuh, silakan pilih jadwal lain'
                : 'Contoh: pelanggan meminta jadwal ulang';

            if (title) {
                title.textContent = isReject ? 'Tolak Reservasi' : 'Batalkan Reservasi';
            }

            if (description) {
                description.textContent = isReject
                    ? 'Reservasi akan ditolak dan pelanggan menerima log WhatsApp penolakan.'
                    : 'Reservasi akan dibatalkan dan pelanggan menerima log WhatsApp pembatalan.';
            }

            if (reasonLabel) {
                reasonLabel.textContent = isReject ? 'Alasan penolakan (wajib)' : 'Alasan pembatalan (opsional)';
            }

            if (submit) {
                submit.textContent = isReject ? 'Tolak Reservasi' : 'Batalkan Reservasi';
            }
        });
    });
</script>
@endpush

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
