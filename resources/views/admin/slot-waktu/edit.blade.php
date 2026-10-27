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
        <div class="card">
            <div class="card-header d-flex align-items-center gap-3 py-4">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="icon-base bx bx-calendar-edit" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">{{ $slot->tanggal->translatedFormat('d M Y') }}</h5>
                    <small class="text-body-secondary">
                        <i class="icon-base bx bx-time me-1" aria-hidden="true"></i>
                        {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                        &bull; Kapasitas {{ $slot->kapasitas }} &bull; Terisi {{ $slot->reservasi_aktif_count }}
                    </small>
                </div>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.slot-waktu.update', $slot) }}" id="form-edit-slot">
                    @include('admin.slot-waktu._edit-form', ['slot' => $slot])
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.page-foot')
