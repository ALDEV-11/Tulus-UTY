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
    .service-detail-label {
        color: var(--bs-secondary-color);
        font-size: .75rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .service-detail-value {
        font-weight: 600;
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Servis',
    'subtitle' => $subtitle,
    'subtitleId' => 'servis-subtitle',
    'icon' => 'bx bx-wrench',
])

<div class="card app-table-card">
    <x-ui.card-header
        title="Daftar Servis"
        subtitle="Pantau antrean, pekerjaan, dan pengambilan kendaraan"
        icon="bx bx-wrench"
        color="primary">
        <x-slot:actions>
            <a href="{{ route('admin.servis.index') }}"
               class="btn btn-sm btn-outline-secondary {{ $filterActive ? '' : 'd-none' }}"
               data-live-search-reset>
                <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.servis.index') }}"
                  class="row g-2 align-items-end"
                  role="search"
                  data-live-search-form
                  data-live-search-targets="#servis-results,#servis-detail-modals"
                  data-live-search-subtitle="#servis-subtitle">
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="q">Cari servis</label>
                    <div class="input-group input-group-merge" data-live-search-group>
                        <span class="input-group-text" data-live-search-icon><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                        <input
                            type="search"
                            class="form-control"
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Kode, nama, nomor HP, atau plat nomor"
                            maxlength="100"
                            autocomplete="off"
                            data-live-search-input>
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
                <div class="col-6 col-sm-6 col-lg-2">
                    <label class="form-label" for="dari">Dari tanggal</label>
                    <input type="date" class="form-control" id="dari" name="dari" value="{{ $dari }}">
                </div>
                <div class="col-6 col-sm-6 col-lg-2">
                    <label class="form-label" for="sampai">Sampai tanggal</label>
                    <input type="date" class="form-control" id="sampai" name="sampai" value="{{ $sampai }}">
                </div>
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base bx bx-search me-1" aria-hidden="true"></i>Cari
                    </button>
                </div>
            </form>
        </x-slot:toolbar>
    </x-ui.card-header>

    <div id="servis-results" class="live-search-results">
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
                            <x-ui.empty-state
                                :title="$filterActive ? 'Tidak ada servis yang cocok' : 'Belum ada servis'"
                                :description="$filterActive
                                    ? 'Coba ubah kata kunci, status, atau rentang tanggal.'
                                    : 'Lakukan check in reservasi yang sudah dikonfirmasi agar kendaraan masuk antrean servis.'"
                                icon="bx bx-wrench"
                                color="primary">
                                @if (! $filterActive)
                                    <x-slot:action>
                                        <a href="{{ route('admin.reservasi.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="icon-base bx bx-calendar-check me-1" aria-hidden="true"></i>Lihat Reservasi
                                        </a>
                                    </x-slot:action>
                                @endif
                            </x-ui.empty-state>
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
</div>

<div id="servis-detail-modals">
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
                        <h6 class="mb-3">Progres Servis</h6>
                        @include('partials.service-timeline', ['servis' => $item])
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">Pelanggan</h6>
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
                                <h6 class="mb-3">Kendaraan</h6>
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
                                <h6 class="mb-3">Waktu Servis</h6>
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
                                <h6 class="mb-3">Layanan</h6>
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
                        <h6 class="mb-1">Catatan Pengerjaan</h6>
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
</div>

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
