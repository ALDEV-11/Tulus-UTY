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
        <div class="card app-form-card">
            <x-ui.card-header
                title="Merk Baru"
                subtitle="Isi nama merk dan atur statusnya"
                icon="bx bx-motorcycle"
                color="info" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.merk-motor.store') }}" id="form-tambah-merk">
                    @include('admin.merk-motor._form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
