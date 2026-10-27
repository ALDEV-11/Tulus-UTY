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
    'icon' => 'bx bxl-whatsapp',
])

<div class="alert alert-info d-flex align-items-start gap-2" role="status">
    <i class="icon-base bx bx-info-circle mt-1" aria-hidden="true"></i>
    <div>
        Log dibuat otomatis saat status reservasi berubah, dan saat admin menekan <strong>Kirim WA</strong>.
        Status <strong>Menunggu</strong> berarti pesan belum dikirim karena gateway WhatsApp belum terhubung.
    </div>
</div>

<div class="card admin-table">
    <div class="card-header py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="icon-base bxl-whatsapp" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Riwayat Pesan WhatsApp</h5>
                    <small class="text-body-secondary">Semua pesan otomatis dan manual</small>
                </div>
            </div>
            @if ($filterActive)
                <a href="{{ route('admin.whatsapp-log.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset Filter
                </a>
            @endif
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        @if ($errors->has('dari') || $errors->has('sampai'))
            <div class="alert alert-danger py-2 px-3 small" role="alert">
                {{ $errors->first('dari') ?: $errors->first('sampai') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.whatsapp-log.index') }}" class="row g-2 align-items-end" role="search">
            <div class="col-12 col-lg-5">
                <label class="form-label" for="q">Cari log</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                    <input
                        type="search"
                        class="form-control"
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Kode reservasi, nama, nomor HP, atau isi pesan"
                        maxlength="100"
                        autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-lg-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="icon-base bx bx-filter-alt me-1" aria-hidden="true"></i>Filter
                </button>
            </div>
            <div class="w-100"></div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="dari">Dari tanggal</label>
                <input type="date" class="form-control @error('dari') is-invalid @enderror" id="dari" name="dari" value="{{ $dari }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="sampai">Sampai tanggal</label>
                <input type="date" class="form-control @error('sampai') is-invalid @enderror" id="sampai" name="sampai" value="{{ $sampai }}">
            </div>
        </form>
    </div>

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
                            <div class="text-center py-8">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class="icon-base bxl-whatsapp icon-lg" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">{{ $filterActive ? 'Tidak ada log yang cocok' : 'Belum ada log WhatsApp' }}</h6>
                                <p class="text-body-secondary mb-0">
                                    {{ $filterActive ? 'Coba ubah kata kunci atau filter.' : 'Log akan muncul saat reservasi dibuat atau admin mengirim WhatsApp.' }}
                                </p>
                            </div>
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

@include('admin.partials.page-foot')
