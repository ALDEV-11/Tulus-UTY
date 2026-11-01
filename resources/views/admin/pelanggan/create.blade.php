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
        <div class="card app-form-card">
            <x-ui.card-header
                title="Informasi Pelanggan"
                subtitle="Isi data akun pelanggan yang akan ditambahkan"
                icon="bx bx-user-plus"
                color="success" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pelanggan.store') }}" id="form-tambah-pelanggan">
                    @include('admin.pelanggan._form')
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4 mt-4 mt-xl-0">
        <div class="card border-0 bg-body-secondary">
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
