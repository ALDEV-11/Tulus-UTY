@php
    $chartId = $chartId ?? 'totalRevenueChart';
    $growthId = $growthId ?? 'growthChart';
@endphp

<div class="card h-100 total-revenue">
    <div class="row row-bordered g-0">
        <div class="col-lg-8">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Total Pendapatan</h5>
                    <p class="card-subtitle mb-0">
                        Pendapatan servis {{ $chartTotalRevenue['year'] }} vs {{ $chartTotalRevenue['previous_year'] }}
                    </p>
                </div>
                <div class="dropdown">
                    <button
                        class="btn btn-sm btn-outline-primary dropdown-toggle"
                        type="button"
                        id="tahun-{{ $chartId }}"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ $chartTotalRevenue['year'] }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="tahun-{{ $chartId }}">
                        @foreach ($chartTotalRevenue['available_years'] as $year)
                            <li>
                                <a
                                    class="dropdown-item {{ $year === $chartTotalRevenue['year'] ? 'active' : '' }}"
                                    href="{{ request()->fullUrlWithQuery(['tahun' => $year]) }}"
                                    @if ($year === $chartTotalRevenue['year']) aria-current="page" @endif>
                                    {{ $year }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if ($chartTotalRevenue['has_data'])
                <div
                    id="{{ $chartId }}"
                    class="px-3 dashboard-chart"
                    role="img"
                    aria-label="Grafik total pendapatan servis {{ $chartTotalRevenue['year'] }} dan {{ $chartTotalRevenue['previous_year'] }}"></div>
            @else
                <div class="text-center text-body-secondary py-8">
                    <i class="icon-base bx bx-bar-chart-alt-2 icon-lg mb-2" aria-hidden="true"></i>
                    <p class="mb-0">Belum ada data pendapatan servis.</p>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-body px-xl-5 py-12 d-flex align-items-center flex-column">
                <div class="text-center mb-6">
                    <span class="badge bg-label-primary">
                        {{ $chartTotalRevenue['period_label'] }} {{ $chartTotalRevenue['year'] }}
                    </span>
                </div>

                <div
                    id="{{ $growthId }}"
                    role="img"
                    aria-label="Pertumbuhan pendapatan {{ $chartTotalRevenue['period_label'] }} {{ $chartTotalRevenue['year'] }} dibanding {{ $chartTotalRevenue['previous_year'] }}"></div>

                <div class="text-center fw-medium my-6">{{ $chartTotalRevenue['growth_note'] }}</div>

                <div class="d-flex gap-4 justify-content-between">
                    <div class="d-flex">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded-2 bg-label-primary">
                                <i class="icon-base bx bx-dollar icon-lg text-primary" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <small>{{ $chartTotalRevenue['year'] }}</small>
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
                            <small>{{ $chartTotalRevenue['previous_year'] }}</small>
                            <h6 class="mb-0 text-nowrap">{{ $chartTotalRevenue['previous_total_label'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
