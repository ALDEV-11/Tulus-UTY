@php
    $pageTitle = 'Servis';
    $filterActive = $search !== '' || $status || $dari || $sampai;
    $nextLabels = [
        'dikerjakan' => 'Mulai Kerjakan',
        'selesai' => 'Tandai Selesai',
        'diambil' => 'Tandai Diambil',
    ];
    $nextIcons = [
        'dikerjakan' => 'bx bx-play-circle',
        'selesai' => 'bx bx-check-circle',
        'diambil' => 'bx bx-package',
    ];
@endphp

@push('styles')
<style>
    .service-status-card {
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .service-status-card:hover,
    .service-status-card:focus-visible {
        border-color: var(--bs-primary);
        box-shadow: 0 .25rem .75rem rgba(105, 108, 255, .18);
        transform: translateY(-1px);
        outline: 0;
    }

    .service-status-card.is-active {
        border-color: var(--bs-primary);
        box-shadow: inset 0 0 0 1px var(--bs-primary);
    }

    .service-detail-label {
        color: var(--bs-secondary-color);
        font-size: .75rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .service-detail-value {
        font-weight: 600;
    }

    @media (prefers-reduced-motion: reduce) {
        .service-status-card {
            transition: none;
        }
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Servis',
    'subtitle' => $subtitle,
    'icon' => 'bx bx-wrench',
])

<div class="row g-3 mb-4">
    @foreach ($statusLabels as $statusValue => $statusLabel)
        @php
            $isActive = $status === $statusValue;
            $filterUrl = route('admin.servis.index', ['status' => $statusValue]);
        @endphp
        <div class="col-6 col-xl-3">
            <a href="{{ $filterUrl }}"
               class="card h-100 text-decoration-none service-status-card {{ $isActive ? 'is-active' : '' }}"
               @if ($isActive) aria-current="true" @endif>
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <p class="text-body-secondary mb-1">{{ $statusLabel }}</p>
                            <h3 class="mb-0">{{ number_format($statusCounts[$statusValue] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <span class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-{{ $statusBadges[$statusValue] ?? 'secondary' }}">
                                <i class="icon-base {{ $statusIcons[$statusValue] ?? 'bx bx-wrench' }}" aria-hidden="true"></i>
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
                        <i class="icon-base bx bx-wrench" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Daftar Servis</h5>
                    <small class="text-body-secondary">Pantau antrean, pekerjaan, dan pengambilan kendaraan</small>
                </div>
            </div>
            @if ($filterActive)
                <a href="{{ route('admin.servis.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset Filter
                </a>
            @endif
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="GET" action="{{ route('admin.servis.index') }}" class="row g-2 align-items-end" role="search">
            <div class="col-12 col-lg-4">
                <label class="form-label" for="q">Cari servis</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                    <input
                        type="search"
                        class="form-control"
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Kode reservasi, nama, nomor HP, atau plat nomor"
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
            <caption class="visually-hidden">Daftar servis beserta reservasi, pelanggan, kendaraan, odometer, dan status pengerjaannya</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">Servis</th>
                    <th scope="col" class="d-none d-md-table-cell">Pelanggan</th>
                    <th scope="col">Kendaraan</th>
                    <th scope="col" class="d-none d-lg-table-cell">Masuk</th>
                    <th scope="col" class="d-none d-md-table-cell">Odometer</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($servis as $item)
                    @php
                        $serviceStatus = $item->status;
                        $nextStatus = $serviceStatus->berikutnya();
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-semibold">Servis #{{ $item->id }}</span><br>
                            <small class="text-body-secondary">
                                {{ $item->reservasi?->kode ?? 'Tanpa reservasi' }}
                            </small>
                        </td>
                        <td class="d-none d-md-table-cell">
                            {{ $item->reservasi?->pelanggan?->name ?? '-' }}<br>
                            <small class="text-body-secondary">{{ $item->reservasi?->pelanggan?->no_hp ?: 'Nomor HP belum diisi' }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $item->kendaraan?->plat_nomor ?? '-' }}</span><br>
                            <small class="text-body-secondary">
                                {{ trim(($item->kendaraan?->merk?->nama ?? '').' '.($item->kendaraan?->model ?? '')) ?: '-' }}
                            </small>
                        </td>
                        <td class="d-none d-lg-table-cell text-nowrap">
                            {{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}
                            @if ($item->tanggal_selesai)
                                <br><small class="text-body-secondary">Selesai {{ $item->tanggal_selesai->translatedFormat('d M Y') }}</small>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell text-nowrap">
                            @if ($item->odometerTampil() !== null)
                                {{ number_format($item->odometerTampil(), 0, ',', '.') }} km
                                <br><small class="text-body-secondary">Awal {{ number_format((int) $item->odometer_awal, 0, ',', '.') }} km</small>
                            @else
                                <span class="text-body-secondary">-</span>
                            @endif
                        </td>
                        <td style="min-width: 9rem;">
                            <span class="badge bg-label-{{ $serviceStatus->badge() }}">{{ $serviceStatus->label() }}</span>
                            <div class="mt-2">
                                @include('partials.service-timeline', ['servis' => $item, 'compact' => true])
                            </div>
                        </td>
                        <td class="text-end text-nowrap">
                            <button
                                type="button"
                                class="btn btn-sm btn-icon btn-text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetailServis{{ $item->id }}"
                                title="Lihat detail servis #{{ $item->id }}"
                                aria-label="Lihat detail servis #{{ $item->id }}">
                                <i class="icon-base bx bx-show" aria-hidden="true"></i>
                            </button>

                            @if ($nextStatus)
                                <form
                                    method="POST"
                                    action="{{ route('admin.servis.status', $item) }}"
                                    id="form-status-servis-{{ $item->id }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Ubah status servis #{{ $item->id }} menjadi {{ $nextStatus->label() }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextStatus->value }}">
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-icon btn-text-success"
                                        title="{{ $nextLabels[$nextStatus->value] ?? $nextStatus->label() }}"
                                        aria-label="{{ $nextLabels[$nextStatus->value] ?? $nextStatus->label() }} servis #{{ $item->id }}">
                                        <i class="icon-base {{ $nextIcons[$nextStatus->value] ?? 'bx bx-right-arrow-alt' }}" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-8">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="icon-base bx bx-wrench icon-lg" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">{{ $filterActive ? 'Tidak ada servis yang cocok' : 'Belum ada servis' }}</h6>
                                <p class="text-body-secondary mb-0">
                                    {{ $filterActive
                                        ? 'Coba ubah kata kunci, status, atau rentang tanggal.'
                                        : 'Lakukan check in reservasi yang sudah dikonfirmasi agar kendaraan masuk antrean servis.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($servis->total() > 0)
        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-body-secondary mb-0">
                Menampilkan {{ $servis->firstItem() }} – {{ $servis->lastItem() }} dari {{ $servis->total() }} servis
            </p>
            @if ($servis->hasPages())
                {{ $servis->links() }}
            @endif
        </div>
    @endif
</div>

@foreach ($servis as $item)
    @php
        $serviceStatus = $item->status;
        $nextStatus = $serviceStatus->berikutnya();
        $layananLabel = $item->reservasi?->layanan->pluck('nama')->join(', ');
    @endphp
    <div class="modal fade" id="modalDetailServis{{ $item->id }}" tabindex="-1" aria-labelledby="modalDetailServis{{ $item->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalDetailServis{{ $item->id }}Label">Detail Servis #{{ $item->id }}</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold">{{ $item->kendaraan?->plat_nomor ?? '-' }}</span>
                            <span class="badge bg-label-{{ $serviceStatus->badge() }}">{{ $serviceStatus->label() }}</span>
                            <small class="text-body-secondary">
                                {{ $item->reservasi?->kode ?? 'Tanpa reservasi' }}
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @error('servis')
                        <div class="alert alert-danger py-2 px-3 small" role="alert">{{ $message }}</div>
                    @enderror
                    @error('status')
                        <div class="alert alert-danger py-2 px-3 small" role="alert">{{ $message }}</div>
                    @enderror
                    @error('odometer')
                        <div class="alert alert-danger py-2 px-3 small" role="alert">{{ $message }}</div>
                    @enderror

                    <div class="border rounded p-3 mb-4">
                        <h6 class="mb-3"><i class="icon-base bx bx-git-commit me-2 text-primary" aria-hidden="true"></i>Progres Servis</h6>
                        @include('partials.service-timeline', ['servis' => $item])
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-user me-2 text-primary" aria-hidden="true"></i>Pelanggan</h6>
                                <div class="mb-2">
                                    <div class="service-detail-label">Nama</div>
                                    <div class="service-detail-value">{{ $item->reservasi?->pelanggan?->name ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="service-detail-label">Nomor HP</div>
                                    <div class="service-detail-value">{{ $item->reservasi?->pelanggan?->no_hp ?: '-' }}</div>
                                </div>
                                <div>
                                    <div class="service-detail-label">Reservasi</div>
                                    <div class="service-detail-value">{{ $item->reservasi?->kode ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-car me-2 text-primary" aria-hidden="true"></i>Kendaraan</h6>
                                <div class="mb-2">
                                    <div class="service-detail-label">Plat nomor</div>
                                    <div class="service-detail-value">{{ $item->kendaraan?->plat_nomor ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="service-detail-label">Merk / Model</div>
                                    <div class="service-detail-value">
                                        {{ trim(($item->kendaraan?->merk?->nama ?? '').' '.($item->kendaraan?->model ?? '')) ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="service-detail-label">Odometer terakhir kendaraan</div>
                                    <div class="service-detail-value">
                                        {{ $item->kendaraan?->odometer_terakhir !== null
                                            ? number_format($item->kendaraan->odometer_terakhir, 0, ',', '.').' km'
                                            : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-calendar me-2 text-primary" aria-hidden="true"></i>Waktu Servis</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="service-detail-label">Tanggal masuk</div>
                                        <div class="service-detail-value">{{ $item->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="service-detail-label">Tanggal selesai</div>
                                        <div class="service-detail-value">{{ $item->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="service-detail-label">Mulai dikerjakan</div>
                                        <div class="service-detail-value">{{ $item->waktu_mulai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="service-detail-label">Selesai dikerjakan</div>
                                        <div class="service-detail-value">{{ $item->waktu_selesai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3"><i class="icon-base bx bx-wrench me-2 text-primary" aria-hidden="true"></i>Layanan</h6>
                                @forelse ($item->reservasi?->layanan ?? [] as $layanan)
                                    <div class="d-flex justify-content-between gap-3 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                        <span>{{ $layanan->nama }}</span>
                                        <small class="text-body-secondary text-nowrap">{{ $layanan->durasi_menit }} menit</small>
                                    </div>
                                @empty
                                    <p class="text-body-secondary mb-0">Belum ada layanan pada reservasi ini.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mt-4">
                        <h6 class="mb-1"><i class="icon-base bx bx-note me-2 text-primary" aria-hidden="true"></i>Catatan Pengerjaan</h6>
                        <p class="small text-body-secondary mb-3">
                            Isi odometer, rincian pekerjaan, dan catatan tambahan untuk pelanggan.
                        </p>

                        @if ($serviceStatus === \App\Enums\StatusServis::Diambil)
                            <div class="row g-3">
                                <div class="col-12 col-sm-4">
                                    <div class="service-detail-label">Odometer</div>
                                    <div class="service-detail-value">
                                        {{ $item->odometer !== null ? number_format($item->odometer, 0, ',', '.').' km' : '-' }}
                                    </div>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <div class="service-detail-label">Rincian pekerjaan</div>
                                    <div class="text-break" style="white-space: pre-line">{{ $item->rincian_pekerjaan ?: '-' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="service-detail-label">Catatan</div>
                                    <div class="text-break" style="white-space: pre-line">{{ $item->catatan ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 px-3 small mt-4 mb-0" role="status">
                                Servis sudah diambil. Catatan pengerjaan dikunci agar riwayat pelanggan tidak berubah.
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.servis.update', $item) }}" id="form-catatan-servis-{{ $item->id }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_modal" value="modalDetailServis{{ $item->id }}">
                                <input type="hidden" name="_record" value="{{ $item->id }}">

                                <div class="row g-3">
                                    <div class="col-12 col-sm-4">
                                        <label class="form-label" for="odometer-servis-{{ $item->id }}">Odometer (km)</label>
                                        <input
                                            type="number"
                                            class="form-control @error('odometer') is-invalid @enderror"
                                            id="odometer-servis-{{ $item->id }}"
                                            name="odometer"
                                            min="0"
                                            max="9999999"
                                            step="1"
                                            value="{{ old('_record') == $item->id ? old('odometer', $item->odometer) : $item->odometer }}"
                                            placeholder="{{ $item->odometer_awal ?? '' }}">
                                        @error('odometer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-8">
                                        <label class="form-label" for="rincian-servis-{{ $item->id }}">Rincian pekerjaan</label>
                                        <textarea
                                            class="form-control @error('rincian_pekerjaan') is-invalid @enderror"
                                            id="rincian-servis-{{ $item->id }}"
                                            name="rincian_pekerjaan"
                                            rows="2"
                                            maxlength="2000"
                                            placeholder="Contoh: Ganti oli, ganti kampas rem depan">{{ old('_record') == $item->id ? old('rincian_pekerjaan', $item->rincian_pekerjaan) : $item->rincian_pekerjaan }}</textarea>
                                        @error('rincian_pekerjaan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="catatan-servis-{{ $item->id }}">Catatan</label>
                                        <textarea
                                            class="form-control @error('catatan') is-invalid @enderror"
                                            id="catatan-servis-{{ $item->id }}"
                                            name="catatan"
                                            rows="2"
                                            maxlength="2000"
                                            placeholder="Contoh: gunakan oli 10W-30, periksa rem belakang">{{ old('_record') == $item->id ? old('catatan', $item->catatan) : $item->catatan }}</textarea>
                                        @error('catatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary mt-3">
                                    <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan Catatan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    @if ($nextStatus)
                        <button
                            type="submit"
                            form="form-status-servis-{{ $item->id }}"
                            class="btn btn-success">
                            <i class="icon-base {{ $nextIcons[$nextStatus->value] ?? 'bx bx-right-arrow-alt' }} me-1" aria-hidden="true"></i>
                            {{ $nextLabels[$nextStatus->value] ?? $nextStatus->label() }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
