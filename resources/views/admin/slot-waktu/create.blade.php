@php
    $pageTitle = 'Buat Slot Massal';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Buat Slot Massal',
    'subtitle' => 'Buat beberapa slot sekaligus untuk rentang tanggal dan jam operasional.',
    'parentTitle' => 'Slot Waktu',
    'currentTitle' => 'Buat Massal',
    'backRoute' => route('admin.slot-waktu.index'),
])

<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="icon-base bx bx-calendar-plus" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">Pembuatan Slot Massal</h5>
                    <small class="text-body-secondary">Slot akan dibuat otomatis untuk setiap hari dalam rentang</small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.slot-waktu.store') }}" id="form-buat-slot">
                    @include('admin.slot-waktu._mass-form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
