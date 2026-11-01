<div class="table-responsive">
    <table class="table align-middle mb-0">
        <caption class="visually-hidden">Daftar pelanggan beserta kontak, jumlah kendaraan, reservasi, dan status akunnya</caption>
        <thead class="table-light">
            <tr>
                <th scope="col">Pelanggan</th>
                <th scope="col">Kontak</th>
                <th scope="col" class="d-none d-md-table-cell text-center">Kendaraan</th>
                <th scope="col" class="d-none d-lg-table-cell text-center">Reservasi</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse ($pelanggans as $pelanggan)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded-circle bg-label-{{ $pelanggan->aktif ? 'success' : 'secondary' }}">
                                    {{ mb_strtoupper(mb_substr($pelanggan->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $pelanggan->name }}</div>
                                <small class="text-body-secondary">
                                    Bergabung {{ $pelanggan->created_at->translatedFormat('d M Y') }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $pelanggan->email }}" class="d-block">{{ $pelanggan->email }}</a>
                        @if ($pelanggan->no_hp)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $pelanggan->no_hp) }}" class="small text-body-secondary">
                                <i class="icon-base bx bx-phone me-1" aria-hidden="true"></i>{{ $pelanggan->no_hp }}
                            </a>
                        @else
                            <span class="small text-body-secondary fst-italic">Belum ada nomor HP</span>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell text-center text-nowrap">
                        <span class="badge rounded-pill bg-label-secondary">{{ $pelanggan->kendaraan_count }}</span>
                    </td>
                    <td class="d-none d-lg-table-cell text-center text-nowrap">
                        <span class="badge rounded-pill bg-label-primary">{{ $pelanggan->reservasi_count }}</span>
                    </td>
                    <td class="text-center">
                        @if ($pelanggan->aktif)
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
                            data-bs-target="#modalEditPelanggan{{ $pelanggan->id }}"
                            title="Edit {{ $pelanggan->name }}"
                            aria-label="Edit {{ $pelanggan->name }}">
                            <i class="icon-base bx bx-edit-alt" aria-hidden="true"></i>
                        </button>

                        @if ($pelanggan->aktif)
                            <form
                                method="POST"
                                action="{{ route('admin.pelanggan.toggle', $pelanggan) }}"
                                class="d-inline"
                                onsubmit="return confirm('Nonaktifkan pelanggan ini? Pelanggan tidak akan bisa login sampai diaktifkan kembali.')">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-icon btn-text-warning"
                                    title="Nonaktifkan {{ $pelanggan->name }}"
                                    aria-label="Nonaktifkan {{ $pelanggan->name }}">
                                    <i class="icon-base bx bx-user-x" aria-hidden="true"></i>
                                </button>
                            </form>
                        @else
                            <form
                                method="POST"
                                action="{{ route('admin.pelanggan.toggle', $pelanggan) }}"
                                class="d-inline"
                                onsubmit="return confirm('Aktifkan pelanggan ini? Pelanggan akan bisa login kembali.')">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-icon btn-text-success"
                                    title="Aktifkan {{ $pelanggan->name }}"
                                    aria-label="Aktifkan {{ $pelanggan->name }}">
                                    <i class="icon-base bx bx-user-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('admin.pelanggan.reset-password', $pelanggan) }}"
                            class="d-inline"
                            onsubmit="return confirm('Reset password pelanggan ini? Password lama langsung tidak berlaku dan password baru akan ditampilkan sekali.')">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="btn btn-sm btn-icon btn-text-secondary"
                                title="Reset password {{ $pelanggan->name }}"
                                aria-label="Reset password {{ $pelanggan->name }}">
                                <i class="icon-base bx bx-key" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        @if ($search === '')
                            <x-ui.empty-state
                                title="Belum ada pelanggan"
                                description="Pelanggan akan muncul di sini setelah mendaftar melalui halaman registrasi."
                                icon="bx bx-group"
                                color="success" />
                        @else
                            <x-ui.empty-state
                                title="Pelanggan tidak ditemukan"
                                :description="'Tidak ada hasil untuk '.$search.'. Coba kata kunci lain.'"
                                icon="bx bx-search-alt"
                                color="secondary">
                                <x-slot:action>
                                    <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-sm btn-outline-secondary" data-live-search-reset>
                                        <i class="icon-base bx bx-x me-1" aria-hidden="true"></i>Hapus pencarian
                                    </a>
                                </x-slot:action>
                            </x-ui.empty-state>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($pelanggans->total() > 0)
    <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <p class="small text-body-secondary mb-0">
            Menampilkan {{ $pelanggans->firstItem() }} – {{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} pelanggan
        </p>
        @if ($pelanggans->hasPages())
            {{ $pelanggans->links() }}
        @endif
    </div>
@endif
