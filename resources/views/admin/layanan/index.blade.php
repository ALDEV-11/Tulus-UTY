@php
    $pageTitle = 'Layanan';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Layanan',
    'subtitle' => $layanans->total().' layanan bengkel terdaftar.',
    'icon' => 'bx bx-wrench',
    'actionModal' => 'modalTambahLayanan',
    'actionLabel' => 'Tambah Layanan',
    'actionIcon' => 'bx bx-plus',
])

<div class="card app-table-card">
    <x-ui.card-header
        title="Daftar Layanan"
        subtitle="Kelola layanan servis bengkel"
        icon="bx bx-wrench"
        color="primary" />

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <caption class="visually-hidden">Daftar layanan bengkel beserta durasi, interval servis, dan statusnya</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">Layanan</th>
                    <th scope="col">Durasi</th>
                    <th scope="col">Interval Servis</th>
                    <th scope="col" class="d-none d-lg-table-cell text-center">Reservasi</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($layanans as $layanan)
                    @php
                        $interval = collect([
                            $layanan->interval_servis_hari ? $layanan->interval_servis_hari.' hari' : null,
                            $layanan->interval_servis_km ? number_format($layanan->interval_servis_km, 0, ',', '.').' km' : null,
                        ])->filter();
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $layanan->nama }}</div>
                            <div class="text-body-secondary small text-truncate" style="max-width: 22rem"
                                 title="{{ $layanan->deskripsi ?: 'Belum ada deskripsi.' }}">
                                {{ $layanan->deskripsi ?: 'Belum ada deskripsi.' }}
                            </div>
                        </td>
                        <td class="text-nowrap">
                            {{ $layanan->durasi_menit }} menit
                        </td>
                        <td class="text-nowrap">
                            @if ($interval->isEmpty())
                                <span class="text-body-secondary">—</span>
                            @else
                                <span class="badge bg-label-info">{{ $interval->join(' / ') }}</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell text-center">
                            <span class="badge rounded-pill bg-label-primary">{{ $layanan->reservasi_count }}</span>
                        </td>
                        <td class="text-center">
                            @if ($layanan->aktif)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button
                                type="button"
                                class="btn btn-sm btn-icon btn-text-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditLayanan{{ $layanan->id }}"
                                title="Edit {{ $layanan->nama }}"
                                aria-label="Edit {{ $layanan->nama }}">
                                <i class="icon-base bx bx-edit-alt" aria-hidden="true"></i>
                            </button>
                            <form
                                method="POST"
                                action="{{ route('admin.layanan.destroy', $layanan) }}"
                                class="d-inline"
                                onsubmit="return confirm('Hapus layanan {{ $layanan->nama }} secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-icon btn-text-danger"
                                    title="Hapus {{ $layanan->nama }}"
                                    aria-label="Hapus {{ $layanan->nama }}">
                                    <i class="icon-base bx bx-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-ui.empty-state
                                title="Belum ada layanan"
                                description="Tambahkan layanan pertama beserta durasi dan interval servisnya.">
                                <x-slot:action>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        id="btn-tambah-layanan-empty"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTambahLayanan">
                                        <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Layanan
                                    </button>
                                </x-slot:action>
                            </x-ui.empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($layanans->total() > 0)
        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-body-secondary mb-0">
                Menampilkan {{ $layanans->firstItem() }} – {{ $layanans->lastItem() }} dari {{ $layanans->total() }} layanan
            </p>
            @if ($layanans->hasPages())
                {{ $layanans->links() }}
            @endif
        </div>
    @endif
</div>

<div class="modal fade" id="modalTambahLayanan" tabindex="-1" aria-labelledby="modalTambahLayananLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.layanan.store') }}">
                <input type="hidden" name="_modal" value="modalTambahLayanan">
                <input type="hidden" name="_record" value="">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalTambahLayananLabel">Tambah Layanan</h5>
                        <small class="text-body-secondary">Lengkapi detail layanan baru.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @include('admin.layanan._form', [
                        'layanan' => $layananBaru,
                        'prefix' => 'tambah-layanan-',
                        'useOld' => old('_modal') === 'modalTambahLayanan',
                        'inModal' => true,
                    ])
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($layanans as $layanan)
    <div class="modal fade" id="modalEditLayanan{{ $layanan->id }}" tabindex="-1" aria-labelledby="modalEditLayanan{{ $layanan->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.layanan.update', $layanan) }}">
                    @method('PUT')
                    <input type="hidden" name="_modal" value="modalEditLayanan{{ $layanan->id }}">
                    <input type="hidden" name="_record" value="{{ $layanan->id }}">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1" id="modalEditLayanan{{ $layanan->id }}Label">Edit Layanan</h5>
                            <small class="text-body-secondary">{{ $layanan->nama }}</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        @include('admin.layanan._form', [
                            'layanan' => $layanan,
                            'prefix' => 'edit-layanan-'.$layanan->id.'-',
                            'useOld' => old('_modal') === 'modalEditLayanan'.$layanan->id,
                            'inModal' => true,
                        ])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
