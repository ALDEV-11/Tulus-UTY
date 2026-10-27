@php
    $pageTitle = 'Dashboard';
    $statusReservasiLabel = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'dikonfirmasi' => 'Dikonfirmasi',
        'check_in' => 'Check In',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $statusReservasiBadge = [
        'menunggu_konfirmasi' => 'warning',
        'dikonfirmasi' => 'primary',
        'check_in' => 'info',
        'selesai' => 'success',
        'dibatalkan' => 'danger',
    ];
    $statusServisLabel = [
        'dijadwalkan' => 'Dijadwalkan',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $statusServisBadge = [
        'dijadwalkan' => 'info',
        'dikerjakan' => 'warning',
        'selesai' => 'success',
        'dibatalkan' => 'danger',
    ];
    $statusReservasiColors = ['#ffab00', '#696cff', '#03c3ec', '#71dd37', '#ff3e1d'];
    $totalStatusReservasi = array_sum($chartStatusReservasi['data']);
@endphp

@push('styles')
<style>
    .dashboard-chart {
        min-height: 300px;
    }

    .dashboard-chart-donut {
        min-height: 250px;
    }

    .dashboard-legend {
        display: grid;
        gap: 0.375rem;
    }

    .dashboard-legend-dot {
        display: inline-block;
        flex: 0 0 auto;
        width: 0.625rem;
        height: 0.625rem;
        border-radius: 50%;
    }

    .dashboard-stat-card .avatar-initial {
        width: 3rem;
        height: 3rem;
        font-size: 1.35rem;
    }

    .dashboard-list > :last-child {
        border-bottom: 0 !important;
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Dashboard',
    'subtitle' => 'Ringkasan operasional bengkel berdasarkan data reservasi, servis, dan pelanggan.',
    'icon' => 'bx bx-home-smile',
])

<div class="row g-4 mb-6">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 dashboard-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="text-body-secondary mb-1">Total Reservasi</p>
                        <h3 class="mb-1">{{ number_format($totalReservasi, 0, ',', '.') }}</h3>
                        <small class="text-warning">
                            <i class="icon-base bx bx-time-five me-1" aria-hidden="true"></i>
                            {{ number_format($reservasiMenunggu, 0, ',', '.') }} menunggu konfirmasi
                        </small>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 dashboard-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="text-body-secondary mb-1">Pendapatan Servis</p>
                        <h3 class="mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                        <small class="text-success">
                            <i class="icon-base bx bx-check-circle me-1" aria-hidden="true"></i>
                            Dari {{ number_format($totalServis, 0, ',', '.') }} servis
                        </small>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="icon-base bx bx-wallet" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 dashboard-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="text-body-secondary mb-1">Pelanggan Aktif</p>
                        <h3 class="mb-1">{{ number_format($pelangganAktif, 0, ',', '.') }} <span class="fs-6 text-body-secondary">/ {{ number_format($pelangganTotal, 0, ',', '.') }}</span></h3>
                        <small class="text-info">
                            <i class="icon-base bx bx-car me-1" aria-hidden="true"></i>
                            {{ number_format($totalKendaraan, 0, ',', '.') }} kendaraan terdaftar
                        </small>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            <i class="icon-base bx bx-group" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 dashboard-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="text-body-secondary mb-1">Servis Aktif</p>
                        <h3 class="mb-1">{{ number_format($servisAktif, 0, ',', '.') }}</h3>
                        <small class="text-primary">
                            <i class="icon-base bx bx-wrench me-1" aria-hidden="true"></i>
                            {{ number_format($servisSelesai, 0, ',', '.') }} servis selesai
                        </small>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                            <i class="icon-base bx bx-wrench" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-6">
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h5 class="mb-1">Tren Reservasi</h5>
                    <p class="card-subtitle mb-0">Jumlah reservasi masuk selama 7 hari terakhir</p>
                </div>
                <span class="badge bg-label-primary">{{ array_sum($chartReservasiHarian['data']) }} reservasi</span>
            </div>
            <div class="card-body">
                <div id="chartReservasiHarian" class="dashboard-chart" role="img" aria-label="Grafik tren reservasi tujuh hari terakhir"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h5 class="mb-1">Status Reservasi</h5>
                    <p class="card-subtitle mb-0">Distribusi seluruh reservasi</p>
                </div>
                @if ($totalStatusReservasi > 0)
                    <span class="badge bg-label-warning">{{ number_format($totalStatusReservasi, 0, ',', '.') }} reservasi</span>
                @endif
            </div>
            <div class="card-body">
                @if ($totalStatusReservasi > 0)
                    <div id="chartStatusReservasi" class="dashboard-chart dashboard-chart-donut" role="img" aria-label="Grafik distribusi status reservasi"></div>

                    <ul class="dashboard-legend list-unstyled mb-0 mt-3" aria-label="Keterangan status reservasi">
                        @foreach ($chartStatusReservasi['labels'] as $index => $label)
                            <li class="d-flex align-items-center gap-2">
                                <span class="dashboard-legend-dot" style="background-color: {{ $statusReservasiColors[$index] ?? '#a1acb8' }};"></span>
                                <span class="flex-grow-1 small text-body-secondary">{{ $label }}</span>
                                <span class="small fw-semibold">{{ number_format($chartStatusReservasi['data'][$index], 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-body-secondary py-8">
                        <i class="icon-base bx bx-pie-chart-alt-2 icon-lg mb-2" aria-hidden="true"></i>
                        <p class="mb-0">Belum ada data reservasi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-6">
    <div class="col-12 col-xl-8">
        @include('partials.revenue-card', [
            'chartTotalRevenue' => $chartTotalRevenue,
            'chartId' => 'totalRevenueChart',
            'growthId' => 'growthChart',
        ])
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1">Layanan Terpopuler</h5>
                <p class="card-subtitle mb-0">Berdasarkan jumlah reservasi</p>
            </div>
            <div class="card-body">
                @if (array_sum($chartLayananPopuler['data']) > 0)
                    <div id="chartLayananPopuler" class="dashboard-chart" role="img" aria-label="Grafik layanan terpopuler"></div>
                @else
                    <div class="text-center text-body-secondary py-8">
                        <i class="icon-base bx bx-bar-chart-alt-2 icon-lg mb-2" aria-hidden="true"></i>
                        <p class="mb-0">Belum ada reservasi layanan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-6">
    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Reservasi Terbaru</h5>
                    <p class="card-subtitle mb-0">5 reservasi terakhir</p>
                </div>
                <a href="{{ route('admin.reservasi.index') }}" class="btn btn-sm btn-outline-primary">Kelola Reservasi</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <caption class="visually-hidden">Daftar reservasi terbaru beserta pelanggan, kendaraan, jadwal, dan statusnya</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Kendaraan</th>
                            <th scope="col">Jadwal</th>
                            <th scope="col" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($reservasiTerbaru as $reservasi)
                            @php
                                $status = $reservasi->status->value;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $reservasi->kode }}</span><br>
                                    <small class="text-body-secondary">{{ $reservasi->created_at->translatedFormat('d M Y') }}</small>
                                </td>
                                <td>{{ $reservasi->pelanggan?->name ?? '-' }}</td>
                                <td>
                                    {{ $reservasi->kendaraan?->plat_nomor ?? '-' }}<br>
                                    <small class="text-body-secondary">{{ $reservasi->kendaraan?->merk?->nama ?? '-' }}</small>
                                </td>
                                <td class="text-nowrap">
                                    {{ $reservasi->slot?->tanggal?->translatedFormat('d M Y') ?? '-' }}<br>
                                    <small class="text-body-secondary">
                                        {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).' – '.substr($reservasi->slot->jam_selesai, 0, 5) : '-' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $statusReservasiBadge[$status] ?? 'secondary' }}">
                                        {{ $statusReservasiLabel[$status] ?? $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-body-secondary py-6">Belum ada reservasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Servis Terbaru</h5>
                    <p class="card-subtitle mb-0">5 servis terakhir</p>
                </div>
                <span class="badge bg-label-success">{{ $servisTerbaru->count() }} data</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <caption class="visually-hidden">Daftar servis terbaru beserta kendaraan, mekanik, biaya, dan statusnya</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Kendaraan</th>
                            <th scope="col">Mekanik</th>
                            <th scope="col">Masuk</th>
                            <th scope="col" class="text-end">Biaya</th>
                            <th scope="col" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($servisTerbaru as $servis)
                            @php
                                $status = $servis->status->value;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $servis->kendaraan?->plat_nomor ?? '-' }}</span><br>
                                    <small class="text-body-secondary">{{ $servis->kendaraan?->merk?->nama ?? '-' }}</small>
                                </td>
                                <td>{{ $servis->mekanik?->name ?? '-' }}</td>
                                <td class="text-nowrap">{{ $servis->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="text-end text-nowrap">Rp {{ number_format((float) $servis->total_biaya, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $statusServisBadge[$status] ?? 'secondary' }}">
                                        {{ $statusServisLabel[$status] ?? $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-body-secondary py-6">Belum ada servis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Slot Mendatang</h5>
                    <p class="card-subtitle mb-0">5 slot terdekat berdasarkan tanggal dan jam</p>
                </div>
                <a href="{{ route('admin.slot-waktu.index') }}" class="btn btn-sm btn-outline-primary">Kelola Slot</a>
            </div>
            <div class="card-body pt-2">
                <div class="dashboard-list">
                    @forelse ($slotMendatang as $slot)
                        @php
                            $isFull = $slot->reservasi_aktif_count >= $slot->kapasitas;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center gap-3 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $isFull ? 'danger' : 'success' }}">
                                        <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $slot->tanggal->translatedFormat('d M Y') }}</div>
                                    <small class="text-body-secondary">
                                        {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-label-{{ $isFull ? 'danger' : 'success' }}">
                                    {{ $slot->reservasi_aktif_count }}/{{ $slot->kapasitas }}
                                </span>
                                <div class="small text-body-secondary mt-1">{{ $isFull ? 'Penuh' : 'Tersedia' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-body-secondary py-8">
                            <i class="icon-base bx bx-calendar-x icon-lg mb-2" aria-hidden="true"></i>
                            <p class="mb-0">Belum ada slot mendatang.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1">Ringkasan Operasional</h5>
                <p class="card-subtitle mb-0">Kondisi data master dan komunikasi</p>
            </div>
            <div class="card-body pt-2">
                <ul class="list-unstyled mb-0 dashboard-list">
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bx-car text-primary me-2" aria-hidden="true"></i>Merk aktif</span>
                        <span class="fw-semibold">{{ number_format($merkAktif, 0, ',', '.') }} / {{ number_format($totalMerk, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bx-wrench text-success me-2" aria-hidden="true"></i>Layanan aktif</span>
                        <span class="fw-semibold">{{ number_format($layananAktif, 0, ',', '.') }} / {{ number_format($totalLayanan, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bx-group text-info me-2" aria-hidden="true"></i>Kendaraan terdaftar</span>
                        <span class="fw-semibold">{{ number_format($totalKendaraan, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bx-calendar text-warning me-2" aria-hidden="true"></i>Slot hari ini</span>
                        <span class="fw-semibold">{{ number_format($slotTersediaHariIni, 0, ',', '.') }} tersedia / {{ number_format($slotHariIni, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bx-calendar-plus text-info me-2" aria-hidden="true"></i>Slot mendatang</span>
                        <span class="fw-semibold">{{ number_format($slotMendatangTotal, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span><i class="icon-base bx bxl-whatsapp text-success me-2" aria-hidden="true"></i>WhatsApp terkirim</span>
                        <span class="fw-semibold">{{ number_format($whatsappTerkirim, 0, ',', '.') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3">
                        <span><i class="icon-base bx bx-time-five text-warning me-2" aria-hidden="true"></i>WhatsApp menunggu</span>
                        <span class="fw-semibold">{{ number_format($whatsappMenunggu, 0, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('dist-dashboard/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reservasiHarianEl = document.querySelector('#chartReservasiHarian');

        if (reservasiHarianEl) {
            new ApexCharts(reservasiHarianEl, {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Public Sans, sans-serif'
                },
                series: [{
                    name: 'Reservasi',
                    data: @json($chartReservasiHarian['data'])
                }],
                xaxis: {
                    categories: @json($chartReservasiHarian['labels']),
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                colors: ['#696cff'],
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                grid: { borderColor: '#eceef1', strokeDashArray: 4 },
                yaxis: {
                    min: 0,
                    tickAmount: 4,
                    labels: { formatter: function (value) { return Math.round(value); } }
                },
                tooltip: {
                    y: { formatter: function (value) { return value + ' reservasi'; } }
                }
            }).render();
        }

        const statusReservasiEl = document.querySelector('#chartStatusReservasi');

        if (statusReservasiEl) {
            const statusReservasiData = @json($chartStatusReservasi['data']);
            const statusReservasiTotal = statusReservasiData.reduce(function (total, value) { return total + value; }, 0);

            new ApexCharts(statusReservasiEl, {
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'Public Sans, sans-serif'
                },
                series: statusReservasiData,
                labels: @json($chartStatusReservasi['labels']),
                colors: @json($statusReservasiColors),
                stroke: { width: 2, colors: ['#ffffff'] },
                dataLabels: { enabled: false },
                legend: { show: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '22px',
                                    fontWeight: 600,
                                    color: '#566a7f'
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    color: '#697a8d',
                                    formatter: function () { return statusReservasiTotal; }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: { formatter: function (value) { return value + ' reservasi'; } }
                }
            }).render();
        }

        const layananPopulerEl = document.querySelector('#chartLayananPopuler');

        if (layananPopulerEl) {
            new ApexCharts(layananPopulerEl, {
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Public Sans, sans-serif'
                },
                series: [{
                    name: 'Reservasi',
                    data: @json($chartLayananPopuler['data'])
                }],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '55%'
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: @json($chartLayananPopuler['labels']),
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                colors: ['#03c3ec'],
                grid: { borderColor: '#eceef1', strokeDashArray: 4 },
                tooltip: {
                    y: { formatter: function (value) { return value + ' reservasi'; } }
                }
            }).render();
        }
    });
</script>

@include('partials.revenue-chart-script', [
    'chartTotalRevenue' => $chartTotalRevenue,
    'chartId' => 'totalRevenueChart',
    'growthId' => 'growthChart',
])
@endpush

@include('admin.partials.page-foot')
