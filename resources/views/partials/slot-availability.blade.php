@php
    $canBookOnline = ! auth()->check() || auth()->user()->role->value === 'pelanggan';
@endphp

<div class="row justify-content-center">
  <div class="col-12 col-lg-10">
    <div class="text-center mb-6">
      <h1 class="mb-2">Cek Slot Servis</h1>
      <p class="text-body-secondary mb-0">
        Pilih tanggal untuk melihat ketersediaan jam servis motor.
      </p>
    </div>

    @if ($error)
      <div class="alert alert-danger" role="alert">
        <i class="icon-base bx bx-error-circle me-1" aria-hidden="true"></i>{{ $error }}
      </div>
    @endif

    <div class="card mb-6">
      <div class="card-body">
        <form method="GET" action="{{ route('slot.publik') }}" class="row g-3 align-items-end">
          <div class="col-12 col-sm-6">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input
              type="date"
              class="form-control"
              id="tanggal"
              name="tanggal"
              value="{{ $date->toDateString() }}">
          </div>
          <div class="col-12 col-sm-6 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="icon-base bx bx-search me-1" aria-hidden="true"></i>Cek Slot
            </button>
            <a href="{{ route('slot.publik') }}" class="btn btn-outline-secondary">Hari Ini</a>
          </div>
        </form>
      </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h5 class="mb-1">{{ $date->translatedFormat('l, d F Y') }}</h5>
        <p class="text-body-secondary mb-0">{{ $summary['total'] }} slot pada tanggal ini.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-label-success">Tersedia: {{ $summary['tersedia'] }}</span>
        <span class="badge bg-label-warning">Hampir penuh: {{ $summary['hampir_penuh'] }}</span>
        <span class="badge bg-label-danger">Penuh: {{ $summary['penuh'] }}</span>
      </div>
    </div>

    @if ($slots->isEmpty())
      <div class="card">
        <div class="card-body text-center py-6">
          <div class="avatar avatar-lg mb-3">
            <span class="avatar-initial rounded-circle bg-label-secondary">
              <i class="icon-base bx bx-calendar-x icon-lg" aria-hidden="true"></i>
            </span>
          </div>
          <h5 class="mb-1">Belum ada slot</h5>
          <p class="text-body-secondary mb-0">
            Belum ada jadwal servis pada tanggal ini. Silakan pilih tanggal lain.
          </p>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach ($slots as $slot)
          @php
            $status = $slot->status_ketersediaan;
            $label = $statusLabels[$status] ?? $status;
            $badge = $statusBadges[$status] ?? 'secondary';
            $isFull = $status === \App\Services\SlotAvailabilityService::STATUS_PENUH;
          @endphp

          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 border-{{ $slot->lewat ? 'secondary' : ($isFull ? 'danger' : ($status === \App\Services\SlotAvailabilityService::STATUS_HAMPIR_PENUH ? 'warning' : 'success')) }}">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <h5 class="mb-0">
                    {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                  </h5>
                  @if ($slot->lewat)
                    <span class="badge bg-label-secondary">Sudah lewat</span>
                  @else
                    <span class="badge bg-label-{{ $badge }}">{{ $label }}</span>
                  @endif
                </div>

                <ul class="list-unstyled small text-body-secondary mb-4">
                  <li class="d-flex justify-content-between border-bottom py-1">
                    <span>Kapasitas</span>
                    <span class="fw-semibold text-body">{{ $slot->kapasitas }}</span>
                  </li>
                  <li class="d-flex justify-content-between border-bottom py-1">
                    <span>Terpakai</span>
                    <span class="fw-semibold text-body">{{ $slot->terpakai }}</span>
                  </li>
                  <li class="d-flex justify-content-between py-1">
                    <span>Sisa</span>
                    <span class="fw-semibold text-body">{{ $slot->sisa }}</span>
                  </li>
                </ul>

                <div class="mt-auto">
                  @if ($slot->lewat)
                    <button type="button" class="btn btn-outline-secondary w-100" disabled>Sudah lewat</button>
                  @elseif ($isFull)
                    <button type="button" class="btn btn-outline-danger w-100" disabled>Slot penuh</button>
                  @elseif (! $canBookOnline)
                    <a href="{{ route('admin') }}" class="btn btn-outline-primary w-100">Buka Dashboard Admin</a>
                  @else
                    <a
                      href="{{ route('booking-saya', ['slot' => $slot->id, 'tanggal' => $date->toDateString()]) }}"
                      class="btn btn-primary w-100">
                      Booking Slot Ini
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <div class="alert alert-info d-flex align-items-start gap-2 mt-6" role="status">
      <i class="icon-base bx bx-info-circle mt-1" aria-hidden="true"></i>
      <div>
        Biaya menyesuaikan merk dan kondisi motor, dikonfirmasi bengkel.
        Slot yang penuh atau sudah lewat tidak dapat dipilih.
      </div>
    </div>
  </div>
</div>
