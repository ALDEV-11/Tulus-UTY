@php
    $pageTitle = 'Tambah Layanan';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Tambah Layanan',
    'subtitle' => 'Tentukan durasi dan interval servis untuk layanan baru.',
    'parentTitle' => 'Layanan',
    'currentTitle' => 'Tambah',
    'backRoute' => route('admin.layanan.index'),
])

<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="icon-base bx bx-wrench" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Informasi Layanan</h5>
                    <small class="text-body-secondary">Isi detail layanan yang akan ditambahkan</small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.layanan.store') }}" id="form-tambah-layanan">
                    @include('admin.layanan._form')
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4 mt-4 mt-xl-0">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle text-primary" aria-hidden="true"></i>
                    Panduan Pengisian
                </h6>
                <ul class="list-unstyled mb-0 small text-body-secondary">
                    <li class="mb-2">
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        <strong>Durasi</strong> menentukan berapa menit slot reservasi diblok untuk layanan ini.
                    </li>
                    <li class="mb-2">
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        <strong>Interval hari</strong> menunjukkan rekomendasi jadwal servis ulang dalam hari (mis. 180 hari = 6 bulan).
                    </li>
                    <li class="mb-2">
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        <strong>Interval km</strong> menunjukkan rekomendasi servis ulang berdasarkan jarak tempuh (mis. 5000 km).
                    </li>
                    <li>
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        Layanan yang <strong>tidak aktif</strong> tidak akan muncul di pilihan reservasi pelanggan.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
