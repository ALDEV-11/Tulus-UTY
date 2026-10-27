<div id="slot-tanggal" class="card slot-day-panel">
    @if ($selectedDate)
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 py-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0">{{ $selectedDate->translatedFormat('l, d F Y') }}</h5>
                    <small class="text-body-secondary">{{ $daySummary['total'] }} slot pada tanggal ini</small>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatSlot">
                    <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Slot
                </button>
                <a href="{{ route('admin.slot-waktu.index', ['bulan' => $bulan]) }}" class="btn btn-sm btn-outline-secondary">
                    Tutup
                </a>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-label-success">{{ $daySummary['tersedia'] }} tersedia</span>
                <span class="badge bg-label-warning">{{ $daySummary['sebagian'] }} sebagian</span>
                <span class="badge bg-label-danger">{{ $daySummary['penuh'] }} penuh</span>
                <span class="badge bg-label-secondary">{{ $daySummary['nonaktif'] }} nonaktif</span>
            </div>

            @if ($daySlots->isNotEmpty())
                <div class="slot-day-list">
                    @foreach ($daySlots as $slot)
                        @php
                            [$statusColor, $statusKey] = $slotStatus($slot);
                            $percent = $slot->kapasitas > 0
                                ? min(100, round(($slot->reservasi_aktif_count / $slot->kapasitas) * 100))
                                : 0;
                            $statusLabel = match ($statusKey) {
                                'tersedia' => 'Tersedia',
                                'sebagian' => 'Sebagian',
                                'penuh' => 'Penuh',
                                default => 'Nonaktif',
                            };
                            $progressColor = match ($statusKey) {
                                'tersedia' => 'success',
                                'sebagian' => 'warning',
                                'penuh' => 'danger',
                                default => 'secondary',
                            };
                        @endphp

                        <div class="slot-day-item d-flex flex-column flex-lg-row align-items-lg-center gap-3 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $statusColor }}">
                                        <i class="icon-base bx bx-time-five" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">
                                        {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                                    </div>
                                    <div class="small text-body-secondary">
                                        Kapasitas {{ $slot->kapasitas }} &bull; {{ $slot->reservasi_aktif_count }} reservasi aktif
                                    </div>
                                    <div
                                        class="progress mt-2"
                                        style="height: 5px; max-width: 18rem;"
                                        role="progressbar"
                                        aria-label="Kapasitas terisi {{ $slot->reservasi_aktif_count }} dari {{ $slot->kapasitas }}"
                                        aria-valuenow="{{ min($slot->reservasi_aktif_count, $slot->kapasitas) }}"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $slot->kapasitas }}">
                                        <div class="progress-bar bg-{{ $progressColor }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-label-{{ $statusColor }}">{{ $statusLabel }}</span>
                                <span class="badge bg-label-secondary">{{ $slot->reservasi_aktif_count }}/{{ $slot->kapasitas }}</span>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-icon btn-text-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditSlot{{ $slot->id }}"
                                    title="Edit slot {{ substr($slot->jam_mulai, 0, 5) }}"
                                    aria-label="Edit slot {{ substr($slot->jam_mulai, 0, 5) }}">
                                    <i class="icon-base bx bx-edit-alt" aria-hidden="true"></i>
                                </button>

                                <form
                                    method="POST"
                                    action="{{ route('admin.slot-waktu.destroy', $slot) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus slot ini? Slot dengan reservasi aktif tidak dapat dihapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="_bulan" value="{{ $bulan }}">
                                    <input type="hidden" name="_tanggal" value="{{ $selectedDateKey }}">
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-icon btn-text-danger"
                                        title="Hapus slot {{ substr($slot->jam_mulai, 0, 5) }}"
                                        aria-label="Hapus slot {{ substr($slot->jam_mulai, 0, 5) }}">
                                        <i class="icon-base bx bx-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                            <i class="icon-base bx bx-calendar-x icon-lg" aria-hidden="true"></i>
                        </span>
                    </div>
                    <h6 class="mb-1">Belum ada slot pada tanggal ini</h6>
                    <p class="text-body-secondary mb-4">Buat slot untuk tanggal {{ $selectedDate->translatedFormat('d F Y') }}.</p>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatSlot">
                        <i class="icon-base bx bx-calendar-plus me-1" aria-hidden="true"></i>Buat Slot untuk Tanggal Ini
                    </button>
                </div>
            @endif
        </div>
    @else
        <div class="card-body text-center py-8">
            <div class="avatar avatar-lg mx-auto mb-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                    <i class="icon-base bx bx-calendar-event icon-lg" aria-hidden="true"></i>
                </span>
            </div>
            <h6 class="mb-1">Pilih tanggal</h6>
            <p class="text-body-secondary mb-0">Klik salah satu tanggal di kalender untuk melihat dan mengelola slot.</p>
        </div>
    @endif
</div>
