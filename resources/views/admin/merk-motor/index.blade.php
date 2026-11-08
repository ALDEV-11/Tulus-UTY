@php
    $pageTitle = 'Merk Motor';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Merk Motor',
    'subtitle' => $merks->total().' merk terdaftar untuk kendaraan pelanggan.',
    'icon' => 'bx bx-motorcycle',
    'actionModal' => 'modalTambahMerk',
    'actionLabel' => 'Tambah Merk',
    'actionIcon' => 'bx bx-plus',
])

<div class="card app-table-card">
    <x-ui.card-header
        title="Daftar Merk"
        subtitle="Kelola merk motor yang tersedia"
        icon="bx bx-motorcycle"
        color="info" />

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <caption class="visually-hidden">Daftar merk motor beserta jumlah kendaraan dan statusnya</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">Merk</th>
                    <th scope="col" class="text-center">Kendaraan</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($merks as $merk)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $merk->aktif ? 'info' : 'secondary' }}">
                                        {{ mb_strtoupper(mb_substr($merk->nama, 0, 1)) }}
                                    </span>
                                </div>
                                <span class="fw-semibold">{{ $merk->nama }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-label-secondary">
                                {{ $merk->kendaraan_count }} kendaraan
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($merk->aktif)
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
                                data-bs-target="#modalEditMerk{{ $merk->id }}"
                                title="Edit {{ $merk->nama }}"
                                aria-label="Edit {{ $merk->nama }}">
                                <i class="icon-base bx bx-edit-alt" aria-hidden="true"></i>
                            </button>
                            <form
                                method="POST"
                                action="{{ route('admin.merk-motor.destroy', $merk) }}"
                                class="d-inline"
                                onsubmit="return confirm('Hapus merk ini? Merk yang sudah dipakai kendaraan akan dinonaktifkan.')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-icon btn-text-danger"
                                    title="Hapus {{ $merk->nama }}"
                                    aria-label="Hapus {{ $merk->nama }}">
                                    <i class="icon-base bx bx-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-ui.empty-state
                                title="Belum ada merk motor"
                                description="Tambahkan merk pertama agar data kendaraan bisa dikelompokkan."
                                icon="bx bx-motorcycle"
                                color="info">
                                <x-slot:action>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        id="btn-tambah-merk-empty"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTambahMerk">
                                        <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Merk
                                    </button>
                                </x-slot:action>
                            </x-ui.empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($merks->total() > 0)
        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-body-secondary mb-0">
                Menampilkan {{ $merks->firstItem() }} – {{ $merks->lastItem() }} dari {{ $merks->total() }} merk
            </p>
            @if ($merks->hasPages())
                {{ $merks->links() }}
            @endif
        </div>
    @endif
</div>

<div class="modal fade" id="modalTambahMerk" tabindex="-1" aria-labelledby="modalTambahMerkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.merk-motor.store') }}">
                <input type="hidden" name="_modal" value="modalTambahMerk">
                <input type="hidden" name="_record" value="">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalTambahMerkLabel">Tambah Merk Motor</h5>
                        <small class="text-body-secondary">Daftarkan merk kendaraan baru.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @include('admin.merk-motor._form', [
                        'merk' => $merkBaru,
                        'prefix' => 'tambah-merk-',
                        'useOld' => old('_modal') === 'modalTambahMerk',
                        'inModal' => true,
                    ])
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($merks as $merk)
    <div class="modal fade" id="modalEditMerk{{ $merk->id }}" tabindex="-1" aria-labelledby="modalEditMerk{{ $merk->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.merk-motor.update', $merk) }}">
                    @method('PUT')
                    <input type="hidden" name="_modal" value="modalEditMerk{{ $merk->id }}">
                    <input type="hidden" name="_record" value="{{ $merk->id }}">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1" id="modalEditMerk{{ $merk->id }}Label">Edit Merk Motor</h5>
                            <small class="text-body-secondary">{{ $merk->nama }}</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        @include('admin.merk-motor._form', [
                            'merk' => $merk,
                            'prefix' => 'edit-merk-'.$merk->id.'-',
                            'useOld' => old('_modal') === 'modalEditMerk'.$merk->id,
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
