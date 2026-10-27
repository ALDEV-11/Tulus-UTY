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
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        {{ mb_strtoupper(mb_substr($merk->nama, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">{{ $merk->nama }}</h5>
                    <small class="text-body-secondary">
                        {{ $merk->kendaraan_count ?? 0 }} kendaraan terdaftar &bull;
                        @if ($merk->aktif)
                            <span class="text-success">Aktif</span>
                        @else
                            <span class="text-secondary">Nonaktif</span>
                        @endif
                    </small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.merk-motor.update', $merk) }}" id="form-edit-merk">
                    @method('PUT')
                    @include('admin.merk-motor._form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
