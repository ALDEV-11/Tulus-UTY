@php
    $pageTitle = 'Edit Pelanggan';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Edit Pelanggan',
    'subtitle' => 'Perbarui data akun '.$pelanggan->name.'.',
    'parentTitle' => 'Pelanggan',
    'currentTitle' => 'Edit',
    'backRoute' => route('admin.pelanggan.index'),
])

<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card app-form-card">
            <x-ui.card-header
                :title="$pelanggan->name"
                subtitle="Ubah informasi akun pelanggan di bawah"
                icon="bx bx-user"
                color="warning" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pelanggan.update', $pelanggan) }}" id="form-edit-pelanggan">
                    @method('PUT')
                    @include('admin.pelanggan._form')
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4 mt-4 mt-xl-0">
        <div class="card border-0 bg-body-secondary">
            <div class="card-body">
                <h6 class="mb-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle" aria-hidden="true"></i>
                    Info Pelanggan
                </h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <span class="fw-semibold">Status akun:</span>
                        @if ($pelanggan->aktif)
                            <span class="badge bg-label-success ms-1">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary ms-1">Nonaktif</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <span class="fw-semibold">Kendaraan:</span>
                        <span class="ms-1">{{ $pelanggan->kendaraan_count ?? 0 }} unit</span>
                    </li>
                    <li class="mb-2">
                        <span class="fw-semibold">Reservasi:</span>
                        <span class="ms-1">{{ $pelanggan->reservasi_count ?? 0 }} reservasi</span>
                    </li>
                    <li>
                        <span class="fw-semibold">Bergabung:</span>
                        <span class="ms-1">{{ $pelanggan->created_at->translatedFormat('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
