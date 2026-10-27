@php
    $pageTitle = 'Tambah Merk Motor';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Tambah Merk Motor',
    'subtitle' => 'Daftarkan merk baru agar kendaraan pelanggan bisa dikelompokkan.',
    'parentTitle' => 'Merk Motor',
    'currentTitle' => 'Tambah',
    'backRoute' => route('admin.merk-motor.index'),
])

<div class="row">
    <div class="col-12 col-xl-6 col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="icon-base bx bx-car" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Merk Baru</h5>
                    <small class="text-body-secondary">Isi nama merk dan atur statusnya</small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.merk-motor.store') }}" id="form-tambah-merk">
                    @include('admin.merk-motor._form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
