@php
    $pageTitle = 'Pelanggan';
@endphp

@include('admin.partials.page-head')

@if (session('new_password'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center gap-3">
            <div class="flex-shrink-0">
                <span class="avatar avatar-sm">
                    <span class="avatar-initial rounded-circle bg-warning text-white">
                        <i class="icon-base bx bx-key" aria-hidden="true"></i>
                    </span>
                </span>
            </div>
            <div>
                <h6 class="alert-heading mb-1">Password baru untuk {{ session('new_password_name') }}</h6>
                <p class="mb-2 small">
                    Catat password sementara ini dan sampaikan kepada pelanggan melalui jalur aman.
                    Password tidak ditampilkan lagi setelah halaman ditinggalkan.
                </p>
                <code class="fs-6 d-inline-block bg-body p-2 rounded border">{{ session('new_password') }}</code>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@include('admin.partials.page-header', [
    'title' => 'Pelanggan',
    'subtitle' => $subtitle,
    'subtitleId' => 'pelanggan-subtitle',
    'icon' => 'bx bx-group',
    'actionModal' => 'modalTambahPelanggan',
    'actionLabel' => 'Tambah Pelanggan',
    'actionIcon' => 'bx bx-user-plus',
])

<div class="card app-table-card">
    <x-ui.card-header
        title="Daftar Pelanggan"
        subtitle="Kelola akun dan data pelanggan"
        icon="bx bx-group"
        color="success">
        <x-slot:actions>
            <a href="{{ route('admin.pelanggan.index') }}"
               class="btn btn-sm btn-outline-secondary {{ $search === '' ? 'd-none' : '' }}"
               data-live-search-reset>
                <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.pelanggan.index') }}"
                  class="row g-2 align-items-end"
                  role="search"
                  data-live-search-form
                  data-live-search-targets="#pelanggan-results,#pelanggan-edit-modals"
                  data-live-search-subtitle="#pelanggan-subtitle">
                <div class="col-12 col-lg-10">
                    <label class="form-label" for="q">Cari pelanggan</label>
                    <div class="input-group input-group-merge" data-live-search-group>
                        <span class="input-group-text" data-live-search-icon><i class="icon-base bx bx-search" aria-hidden="true"></i></span>
                        <input
                            type="search"
                            class="form-control"
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Nama, email, atau nomor HP"
                            maxlength="100"
                            autocomplete="off"
                            data-live-search-input>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base bx bx-search me-1" aria-hidden="true"></i>Cari
                    </button>
                </div>
            </form>
        </x-slot:toolbar>
    </x-ui.card-header>

    <div id="pelanggan-results" class="live-search-results" role="region" aria-label="Hasil pencarian pelanggan">
        @include('admin.pelanggan._results')
    </div>
</div>

<div id="pelanggan-edit-modals">
    @include('admin.pelanggan._edit-modals')
</div>

<div class="modal fade" id="modalTambahPelanggan" tabindex="-1" aria-labelledby="modalTambahPelangganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pelanggan.store') }}">
                <input type="hidden" name="_modal" value="modalTambahPelanggan">
                <input type="hidden" name="_record" value="">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalTambahPelangganLabel">Tambah Pelanggan</h5>
                        <small class="text-body-secondary">Buat akun pelanggan baru.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @include('admin.pelanggan._form', [
                        'pelanggan' => $pelangganBaru,
                        'prefix' => 'tambah-pelanggan-',
                        'useOld' => old('_modal') === 'modalTambahPelanggan',
                        'inModal' => true,
                    ])
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
