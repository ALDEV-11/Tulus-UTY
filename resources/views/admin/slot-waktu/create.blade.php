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
        <div class="card app-form-card">
            <x-ui.card-header
                title="Pembuatan Slot Massal"
                subtitle="Slot akan dibuat otomatis untuk setiap hari dalam rentang"
                icon="bx bx-calendar-plus"
                color="warning" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.slot-waktu.store') }}" id="form-buat-slot">
                    @include('admin.slot-waktu._mass-form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
