@php
    $pageTitle = 'Slot Waktu';
    $selectedDateKey = $selectedDate?->toDateString();
    $slotStatus = static function (\App\Models\SlotWaktu $slot): array {
        if (! $slot->aktif) {
            return ['secondary', 'nonaktif'];
        }

        if ($slot->reservasi_aktif_count >= $slot->kapasitas) {
            return ['danger', 'penuh'];
        }

        if ($slot->reservasi_aktif_count > 0) {
            return ['warning', 'sebagian'];
        }

        return ['success', 'tersedia'];
    };
    $subtitle = $monthSummary['total'].' slot pada '.$month->translatedFormat('F Y').'.';
@endphp

@push('styles')
<style>
    #slot-tanggal {
        scroll-margin-top: 6rem;
    }

    .slot-calendar {
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0.375rem;
    }

    .slot-calendar thead th {
        padding: 0 0 0.5rem;
        border: 0;
        color: var(--bs-secondary-color);
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-align: center;
        text-transform: uppercase;
    }

    .slot-calendar tbody td {
        width: calc(100% / 7);
        padding: 0;
        border: 0;
        vertical-align: top;
    }

    .slot-calendar-day {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        min-height: 5.5rem;
        padding: 0.5rem 0.25rem;
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: 0.75rem;
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        text-align: center;
        text-decoration: none;
        transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
    }

    .slot-calendar-day:hover,
    .slot-calendar-day:focus-visible {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-body-color);
        outline: 0;
    }

    .slot-calendar-day:focus-visible {
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
    }

    .slot-calendar-day.is-today {
        border-color: var(--bs-primary);
        box-shadow: inset 0 0 0 1px var(--bs-primary);
    }

    .slot-calendar-day.is-selected,
    .slot-calendar-day.is-selected:hover,
    .slot-calendar-day.is-selected:focus-visible {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary);
        color: #fff;
        box-shadow: 0 0.25rem 0.75rem rgba(105, 108, 255, 0.35);
    }

    .slot-calendar-date {
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1;
    }

    .slot-calendar-dots {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.1875rem;
        min-height: 0.5rem;
    }

    .slot-calendar-dot {
        display: inline-block;
        flex: 0 0 auto;
        width: 0.375rem;
        height: 0.375rem;
        border-radius: 50%;
    }

    .slot-calendar-more {
        color: var(--bs-secondary-color);
        font-size: 0.625rem;
        line-height: 1;
    }

    .slot-calendar-count {
        color: var(--bs-secondary-color);
        font-size: 0.6875rem;
        line-height: 1.1;
    }

    .slot-calendar-empty {
        color: var(--bs-secondary-color);
        font-size: 0.75rem;
        line-height: 1;
    }

    .slot-calendar-day.is-selected .slot-calendar-count,
    .slot-calendar-day.is-selected .slot-calendar-more,
    .slot-calendar-day.is-selected .slot-calendar-empty {
        color: rgba(255, 255, 255, 0.85);
    }

    .slot-calendar-outside .slot-calendar-day {
        background-color: transparent;
        opacity: 0.45;
    }

    .slot-calendar-outside .slot-calendar-day:hover,
    .slot-calendar-outside .slot-calendar-day:focus-visible {
        opacity: 1;
    }

    .slot-calendar-legend-dot {
        width: 0.5rem;
        height: 0.5rem;
    }

    .slot-day-item:last-child {
        border-bottom: 0 !important;
    }

    @media (max-width: 575.98px) {
        .slot-calendar {
            border-spacing: 0.25rem;
        }

        .slot-calendar thead th {
            font-size: 0.625rem;
        }

        .slot-calendar-day {
            gap: 0.1875rem;
            min-height: 4.5rem;
            padding: 0.375rem 0.125rem;
            border-radius: 0.625rem;
        }

        .slot-calendar-date {
            font-size: 0.8125rem;
        }

        .slot-calendar-dot {
            width: 0.3125rem;
            height: 0.3125rem;
        }

        .slot-calendar-count {
            font-size: 0.625rem;
        }
    }
</style>
@endpush

@include('admin.partials.page-head')

@include('admin.partials.page-header', [
    'title' => 'Slot Waktu',
    'subtitle' => $subtitle,
    'icon' => 'bx bx-calendar',
    'actionModal' => 'modalBuatSlot',
    'actionLabel' => 'Buat Slot Massal',
    'actionIcon' => 'bx bx-calendar-plus',
])

@include('admin.slot-waktu._calendar')

@include('admin.slot-waktu._day-panel')

<div class="modal fade" id="modalBuatSlot" tabindex="-1" aria-labelledby="modalBuatSlotLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.slot-waktu.store') }}">
                <input type="hidden" name="_modal" value="modalBuatSlot">
                <input type="hidden" name="_record" value="">
                <input type="hidden" name="_bulan" value="{{ old('_bulan', $bulan) }}">
                <input type="hidden" name="_tanggal" value="{{ old('_tanggal', $selectedDateKey) }}">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="modalBuatSlotLabel">Buat Slot Massal</h5>
                        <small class="text-body-secondary">Slot dibuat otomatis untuk setiap hari dalam rentang.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    @include('admin.slot-waktu._mass-form', [
                        'prefix' => 'buat-slot-',
                        'useOld' => old('_modal') === 'modalBuatSlot',
                        'inModal' => true,
                        'defaultTanggalMulai' => $selectedDateKey,
                        'defaultTanggalSelesai' => $selectedDateKey,
                    ])
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($daySlots as $slot)
    <div class="modal fade" id="modalEditSlot{{ $slot->id }}" tabindex="-1" aria-labelledby="modalEditSlot{{ $slot->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.slot-waktu.update', $slot) }}">
                    <input type="hidden" name="_modal" value="modalEditSlot{{ $slot->id }}">
                    <input type="hidden" name="_record" value="{{ $slot->id }}">
                    <input type="hidden" name="_bulan" value="{{ old('_bulan', $bulan) }}">
                    <input type="hidden" name="_tanggal" value="{{ old('_tanggal', $selectedDateKey) }}">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1" id="modalEditSlot{{ $slot->id }}Label">Edit Slot Waktu</h5>
                            <small class="text-body-secondary">
                                {{ $slot->tanggal->translatedFormat('l, d F Y') }} &bull;
                                {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        @include('admin.slot-waktu._edit-form', [
                            'slot' => $slot,
                            'prefix' => 'edit-slot-'.$slot->id.'-',
                            'useOld' => old('_modal') === 'modalEditSlot'.$slot->id,
                            'inModal' => true,
                        ])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@include('admin.partials.modal-auto-open')

@include('admin.partials.page-foot')
