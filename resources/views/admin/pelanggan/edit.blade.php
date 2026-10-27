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
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="icon-base bx bx-user" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">{{ $pelanggan->name }}</h5>
                    <small class="text-body-secondary">Ubah informasi akun pelanggan di bawah</small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.pelanggan.update', $pelanggan) }}" id="form-edit-pelanggan">
                    @method('PUT')
                    @include('admin.pelanggan._form')
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4 mt-4 mt-xl-0">
        <div class="card border-0 bg-label-warning">
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
