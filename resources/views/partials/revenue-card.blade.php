@php
    $chartId = $chartId ?? 'totalRevenueChart';
    $growthId = $growthId ?? 'growthChart';
@endphp

<div class="card h-100 total-revenue">
    <div class="row row-bordered g-0">
        <div class="col-lg-8">
            <x-ui.card-header
                title="Total Pendapatan"
                subtitle="Pendapatan servis {{ $chartTotalRevenue['current_month_label'] }} vs {{ $chartTotalRevenue['previous_month_label'] }}"
                icon="bx bx-dollar-circle"
                color="success">
            </x-ui.card-header>

            @if ($chartTotalRevenue['has_data'])
                <div
                    id="{{ $chartId }}"
                    class="px-3 dashboard-chart"
                    role="img"
                    aria-label="Grafik total pendapatan servis {{ $chartTotalRevenue['current_month_label'] }} dan {{ $chartTotalRevenue['previous_month_label'] }}"></div>
            @else
                <x-ui.empty-state
                    title="Belum ada data pendapatan"
                    description="Data pendapatan akan muncul setelah servis selesai."
                    icon="bx bx-bar-chart-alt-2"
                    color="success" />
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-body px-xl-5 py-12 d-flex align-items-center flex-column">
                <div class="text-center mb-6">
                    <span class="badge bg-label-primary">
                        {{ $chartTotalRevenue['current_month_label'] }}
                    </span>
                </div>

                <div
                    id="{{ $growthId }}"
                    role="img"
                    aria-label="Pertumbuhan pendapatan {{ $chartTotalRevenue['current_month_label'] }} dibanding {{ $chartTotalRevenue['previous_month_label'] }}"></div>

                <div class="text-center fw-medium my-6">{{ $chartTotalRevenue['growth_note'] }}</div>

                <div class="d-flex gap-4 justify-content-between">
                    <div class="d-flex">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded-2 bg-label-primary">
                                <i class="icon-base bx bx-dollar icon-lg text-primary" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <small>Bulan Ini</small>
                            <h6 class="mb-0 text-nowrap">{{ $chartTotalRevenue['current_total_label'] }}</h6>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded-2 bg-label-info">
                                <i class="icon-base bx bx-wallet icon-lg text-info" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <small>Bulan Lalu</small>
                            <h6 class="mb-0 text-nowrap">{{ $chartTotalRevenue['previous_total_label'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
