@php
    $pageTitle = 'Log WhatsApp';
    $filterActive = $search !== '' || $status || $jenis || $dari || $sampai;
    $jenisLabels = collect(\App\Enums\JenisWhatsapp::cases())
        ->mapWithKeys(fn ($jenis) => [$jenis->value => $jenis->label()]);
@endphp

@push('styles')
<style>
    .reservation-log-message {
        min-width: 16rem;
        white-space: normal;
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Log WhatsApp',
    'subtitle' => $logs->total().' log pesan WhatsApp tercatat.',
    'subtitleId' => 'whatsapp-log-subtitle',
    'icon' => 'bx bxl-whatsapp',
])

<div class="card app-table-card">
    <x-ui.card-header
        title="Riwayat Pesan WhatsApp"
        subtitle="Semua pesan otomatis dan manual"
        icon="bx bxl-whatsapp"
        color="success">
        <x-slot:actions>
            <a href="{{ route('admin.whatsapp-log.index') }}"
               class="btn btn-sm btn-outline-secondary {{ $filterActive ? '' : 'd-none' }}"
               data-live-search-reset>
                <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            @if ($errors->has('dari') || $errors->has('sampai'))
                <div class="alert alert-danger py-2 px-3 small" role="alert">
                    {{ $errors->first('dari') ?: $errors->first('sampai') }}
                </div>
            @endif

            <form method="GET" action="{{ route('admin.whatsapp-log.index') }}"
                  class="row g-2 align-items-end"
                  role="search"
                  data-live-search-form
                  data-live-search-targets="#whatsapp-log-results"
                  data-live-search-subtitle="#whatsapp-log-subtitle">
                <div class="col-12 col-lg-3">
                    <label class="form-label" for="q">Cari log</label>
                    <div class="input-group input-group-merge" data-live-search-group>
                        <span class="input-group-text" data-live-search-icon><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                        <input
                            type="search"
                            class="form-control"
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Kode, nama, nomor HP, atau isi pesan"
                            maxlength="100"
                            autocomplete="off"
                            data-live-search-input>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="jenis">Jenis pesan</label>
                    <select class="form-select" id="jenis" name="jenis">
                        <option value="">Semua jenis</option>
                        @foreach ($jenisLabels as $jenisValue => $jenisLabel)
                            <option value="{{ $jenisValue }}" @selected($jenis === $jenisValue)>{{ $jenisLabel }}</option>
                        @endforeach
                    </select>
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
                <div class="col-6 col-lg-2">
                    <label class="form-label" for="dari">Dari tanggal</label>
                    <input type="date" class="form-control @error('dari') is-invalid @enderror" id="dari" name="dari" value="{{ $dari }}">
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label" for="sampai">Sampai tanggal</label>
                    <input type="date" class="form-control @error('sampai') is-invalid @enderror" id="sampai" name="sampai" value="{{ $sampai }}">
                </div>
                <div class="col-12 col-lg-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base bx bx-search me-1" aria-hidden="true"></i>Cari
                    </button>
                </div>
            </form>
        </x-slot:toolbar>
    </x-ui.card-header>

    <div id="whatsapp-log-results" class="live-search-results">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <caption class="visually-hidden">Daftar log WhatsApp beserta tujuan, jenis, pesan, reservasi, dan status pengirimannya</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">Waktu</th>
                    <th scope="col">Tujuan</th>
                    <th scope="col">Jenis</th>
                    <th scope="col">Pesan</th>
                    <th scope="col">Reservasi</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col">Terkirim</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($logs as $log)
                    <tr>
                        <td class="text-nowrap">
                            <span class="fw-semibold">{{ $log->created_at?->translatedFormat('d M Y') ?? '-' }}</span><br>
                            <small class="text-body-secondary">{{ $log->created_at?->format('H:i') ?? '-' }}</small>
                        </td>
                        <td class="text-nowrap">
                            {{ $log->nomor_tujuan }}<br>
                            <small class="text-body-secondary">{{ $log->user?->name ?? '-' }}</small>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge bg-label-primary">{{ $log->jenis?->label() ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="d-inline-block reservation-log-message" title="{{ $log->pesan }}">
                                {{ \Illuminate\Support\Str::limit($log->pesan, 140) }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            @if ($log->reservasi)
                                <a href="{{ route('admin.reservasi.index', ['q' => $log->reservasi->kode]) }}" class="fw-semibold text-decoration-none">
                                    {{ $log->reservasi->kode }}
                                </a>
                            @else
                                <span class="text-body-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-{{ $statusBadges[$log->status] ?? 'secondary' }}">
                                {{ $statusLabels[$log->status] ?? $log->status }}
                            </span>
                        </td>
                        <td class="text-nowrap small text-body-secondary">
                            {{ $log->terkirim_pada?->translatedFormat('d M Y H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-ui.empty-state
                                :title="$filterActive ? 'Tidak ada log yang cocok' : 'Belum ada log WhatsApp'"
                                :description="$filterActive ? 'Coba ubah kata kunci atau filter.' : 'Log akan muncul saat reservasi dibuat atau admin mengirim WhatsApp.'"
                                icon="bx bxl-whatsapp"
                                color="success" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->total() > 0)
        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-body-secondary mb-0">
                Menampilkan {{ $logs->firstItem() }} – {{ $logs->lastItem() }} dari {{ $logs->total() }} log
            </p>
            @if ($logs->hasPages())
                {{ $logs->links() }}
            @endif
        </div>
    @endif
    </div>
</div>

@include('admin.partials.page-foot')
