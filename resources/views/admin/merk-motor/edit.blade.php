@php
    $pageTitle = 'Edit Merk Motor';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Edit Merk Motor',
    'subtitle' => 'Perbarui nama atau status merk '.$merk->nama.'.',
    'parentTitle' => 'Merk Motor',
    'currentTitle' => 'Edit',
    'backRoute' => route('admin.merk-motor.index'),
])

<div class="row">
    <div class="col-12 col-xl-6 col-lg-8">
        <div class="card app-form-card">
            <x-ui.card-header
                :title="$merk->nama"
                :subtitle="($merk->kendaraan_count ?? 0).' kendaraan terdaftar · '.($merk->aktif ? 'Aktif' : 'Nonaktif')"
                icon="bx bx-edit"
                color="warning" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.merk-motor.update', $merk) }}" id="form-edit-merk">
                    @method('PUT')
                    @include('admin.merk-motor._form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
