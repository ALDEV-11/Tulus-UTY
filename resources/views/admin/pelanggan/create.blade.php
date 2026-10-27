@php
    $pageTitle = 'Tambah Pelanggan';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Tambah Pelanggan',
    'subtitle' => 'Buat akun pelanggan baru yang dapat login dan membuat reservasi.',
    'parentTitle' => 'Pelanggan',
    'currentTitle' => 'Tambah',
    'backRoute' => route('admin.pelanggan.index'),
])

<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="icon-base bx bx-user-plus" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Informasi Pelanggan</h5>
                    <small class="text-body-secondary">Isi data akun pelanggan yang akan ditambahkan</small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.pelanggan.store') }}" id="form-tambah-pelanggan">
                    @include('admin.pelanggan._form')
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
                        Email harus unik dan digunakan pelanggan untuk login.
                    </li>
                    <li class="mb-2">
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        Password minimal 8 karakter dan harus sama dengan konfirmasinya.
                    </li>
                    <li class="mb-2">
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        Nomor HP dipakai sebagai tujuan notifikasi WhatsApp.
                    </li>
                    <li>
                        <i class="icon-base bx bx-check text-success me-1" aria-hidden="true"></i>
                        Akun nonaktif tetap tersimpan, tetapi tidak dapat login.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
