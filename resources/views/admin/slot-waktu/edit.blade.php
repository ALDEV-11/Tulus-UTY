@php
    $pageTitle = 'Edit Slot Waktu';
@endphp
@include('admin.partials.page-head')

@include('admin.partials.form-header', [
    'title' => 'Edit Slot Waktu',
    'subtitle' => $slot->tanggal->translatedFormat('l, d F Y').' pukul '.substr($slot->jam_mulai, 0, 5).' - '.substr($slot->jam_selesai, 0, 5).'.',
    'parentTitle' => 'Slot Waktu',
    'currentTitle' => 'Edit',
    'backRoute' => route('admin.slot-waktu.index'),
])

<div class="row">
    <div class="col-12 col-xl-7 col-lg-9">
        <div class="card app-form-card">
            <x-ui.card-header
                :title="$slot->tanggal->translatedFormat('d M Y')"
                :subtitle="substr($slot->jam_mulai, 0, 5).' – '.substr($slot->jam_selesai, 0, 5).' · Kapasitas '.$slot->kapasitas.' · Terisi '.$slot->reservasi_aktif_count"
                icon="bx bx-calendar-edit"
                color="warning" />
            <div class="card-body">
                <form method="POST" action="{{ route('admin.slot-waktu.update', $slot) }}" id="form-edit-slot">
                    @include('admin.slot-waktu._edit-form', ['slot' => $slot])
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
