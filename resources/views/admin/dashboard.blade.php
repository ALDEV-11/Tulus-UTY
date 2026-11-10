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
        'dibatalkan' => 'secondary',
    ];

    $statusReservasiColors = ['#ffab00', '#696cff', '#03c3ec', '#71dd37', '#8592a3'];
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
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Dashboard',
    'subtitle' => 'Ringkasan operasional bengkel.',
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
                            <i class="icon-base bx bx-motorcycle me-1" aria-hidden="true"></i>
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
            <x-ui.card-header
                title="Tren Reservasi"
                subtitle="Jumlah reservasi masuk selama 7 hari terakhir"
                icon="bx bx-line-chart"
                color="primary">
                <x-slot:actions>
                    <span class="badge bg-label-primary">{{ number_format(array_sum($chartReservasiHarian['data']), 0, ',', '.') }} reservasi</span>
                </x-slot:actions>
            </x-ui.card-header>

            <div class="card-body">
                @if (array_sum($chartReservasiHarian['data']) > 0)
                    <div
                        id="chartReservasiHarian"
                        class="dashboard-chart"
                        role="img"
                        aria-label="Grafik tren reservasi tujuh hari terakhir"></div>
                @else
                    <x-ui.empty-state
                        title="Belum ada reservasi"
                        description="Reservasi tujuh hari terakhir akan muncul di sini."
                        icon="bx bx-line-chart"
                        color="primary" />
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <x-ui.card-header
                title="Status Reservasi"
                subtitle="Distribusi seluruh reservasi"
                icon="bx bx-pie-chart-alt-2"
                color="warning">
                <x-slot:actions>
                    @if ($totalStatusReservasi > 0)
                        <span class="badge bg-label-warning">{{ number_format($totalStatusReservasi, 0, ',', '.') }} reservasi</span>
                    @endif
                </x-slot:actions>
            </x-ui.card-header>

            <div class="card-body">
                @if ($totalStatusReservasi > 0)
                    <div
                        id="chartStatusReservasi"
                        class="dashboard-chart dashboard-chart-donut"
                        role="img"
                        aria-label="Grafik distribusi status reservasi"></div>

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
                    <x-ui.empty-state
                        title="Belum ada data reservasi"
                        description="Distribusi status akan muncul setelah ada reservasi."
                        icon="bx bx-pie-chart-alt-2"
                        color="warning" />
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-6">
    <div class="col-12">
        @include('partials.revenue-card', [
            'chartTotalRevenue' => $chartTotalRevenue,
            'chartId' => 'totalRevenueChart',
            'growthId' => 'growthChart',
        ])
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card h-100 app-table-card">
            <x-ui.card-header
                title="Reservasi Terbaru"
                subtitle="5 reservasi terakhir"
                icon="bx bx-calendar-check"
                color="primary">
                <x-slot:actions>
                    <a href="{{ route('admin.reservasi.index') }}" class="btn btn-sm btn-outline-primary">Kelola Reservasi</a>
                </x-slot:actions>
            </x-ui.card-header>

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
                                <td colspan="5">
                                    <x-ui.empty-state
                                        title="Belum ada reservasi"
                                        description="Reservasi pelanggan akan muncul di sini."
                                        icon="bx bx-calendar-check"
                                        color="primary" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script src="{{ asset('dist-dashboard/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cardColor = config.colors.cardColor;
        const borderColor = config.colors.borderColor;
        const labelColor = config.colors.textMuted;
        const headingColor = config.colors.headingColor;
        const fontFamily = config.fontFamily;

        const reservasiHarianEl = document.querySelector('#chartReservasiHarian');

        if (reservasiHarianEl) {
            new ApexCharts(reservasiHarianEl, {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: fontFamily
                },
                series: [{
                    name: 'Reservasi',
                    data: @json($chartReservasiHarian['data'])
                }],
                xaxis: {
                    categories: @json($chartReservasiHarian['labels']),
                    labels: {
                        style: {
                            colors: labelColor,
                            fontFamily: fontFamily,
                            fontSize: '13px'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    min: 0,
                    tickAmount: 4,
                    labels: {
                        formatter: function (value) { return Math.round(value); },
                        style: {
                            colors: labelColor,
                            fontFamily: fontFamily,
                            fontSize: '13px'
                        }
                    }
                },
                colors: [config.colors.primary],
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
                grid: { borderColor: borderColor, strokeDashArray: 7 },
                tooltip: {
                    y: { formatter: function (value) { return value + ' reservasi'; } }
                },
                states: {
                    hover: { filter: { type: 'none' } },
                    active: { filter: { type: 'none' } }
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
                    fontFamily: fontFamily
                },
                series: statusReservasiData,
                labels: @json($chartStatusReservasi['labels']),
                colors: @json($statusReservasiColors),
                stroke: { width: 2, colors: [cardColor] },
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
                                    color: headingColor
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    color: labelColor,
                                    formatter: function () { return statusReservasiTotal; }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: { formatter: function (value) { return value + ' reservasi'; } }
                },
                states: {
                    hover: { filter: { type: 'none' } },
                    active: { filter: { type: 'none' } }
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
