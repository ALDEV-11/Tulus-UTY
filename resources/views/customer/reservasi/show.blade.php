@extends('customer.layouts.app')

@section('title', 'Detail Reservasi')

@section('content')
  @php
    $statusValue = $reservasi->status->value;
    $alasanPenolakan = $reservasi->alasanPenolakan();
    $statusLabels = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'dikonfirmasi' => 'Dikonfirmasi',
        'check_in' => 'Check In',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $statusBadges = [
        'menunggu_konfirmasi' => 'warning',
        'dikonfirmasi' => 'primary',
        'check_in' => 'info',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];
    $statusLabel = $alasanPenolakan ? 'Ditolak' : ($statusLabels[$statusValue] ?? $statusValue);
    $statusBadge = $alasanPenolakan ? 'danger' : ($statusBadges[$statusValue] ?? 'secondary');
  @endphp

  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
    <div>
      <h4 class="mb-1">Detail Reservasi</h4>
      <p class="text-body-secondary mb-0">Kode Booking {{ $reservasi->kode }}</p>
    </div>
    <a href="{{ route('booking-saya') }}" class="btn btn-outline-secondary">
      <i class="icon-base bx bx-arrow-back me-1" aria-hidden="true"></i>Kembali ke Booking Saya
    </a>
  </div>

  @if ($alasanPenolakan)
    <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
      <i class="icon-base bx bx-error-circle mt-1" aria-hidden="true"></i>
      <div>
        <strong>Reservasi ditolak oleh bengkel.</strong><br>
        Alasan: {{ $alasanPenolakan }}
      </div>
    </div>
  @endif

  <div class="row g-4">
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-3">
          <h5 class="mb-0">Informasi Reservasi</h5>
          <span class="badge bg-label-{{ $statusBadge }}">{{ $statusLabel }}</span>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Kode Booking</dt>
            <dd class="col-sm-8">{{ $reservasi->kode }}</dd>

            <dt class="col-sm-4">Tanggal</dt>
            <dd class="col-sm-8">{{ $reservasi->slot?->tanggal?->translatedFormat('l, d F Y') ?? '-' }}</dd>

            <dt class="col-sm-4">Jam</dt>
            <dd class="col-sm-8">
              {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).' – '.substr($reservasi->slot->jam_selesai, 0, 5) : '-' }}
            </dd>

            <dt class="col-sm-4">Kendaraan</dt>
            <dd class="col-sm-8">
              {{ $reservasi->kendaraan?->plat_nomor ?? '-' }}<br>
              <span class="text-body-secondary">
                {{ trim(($reservasi->kendaraan?->merk?->nama ?? '').' '.($reservasi->kendaraan?->model ?? '')) ?: '-' }}
              </span>
            </dd>

            <dt class="col-sm-4">Keluhan</dt>
            <dd class="col-sm-8">{{ $reservasi->keluhan ?: '-' }}</dd>

            <dt class="col-sm-4">Dibuat</dt>
            <dd class="col-sm-8">{{ $reservasi->created_at?->translatedFormat('d F Y H:i') ?? '-' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Layanan</h5>
        </div>
        <div class="card-body">
          @if ($reservasi->layanan->isEmpty())
            <p class="text-body-secondary mb-0">Belum ada layanan yang dipilih.</p>
          @else
            <ul class="list-group list-group-flush mb-3">
              @foreach ($reservasi->layanan as $layanan)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <span>{{ $layanan->nama }}</span>
                  <span class="text-body-secondary small">{{ $layanan->durasi_menit }} menit</span>
                </li>
              @endforeach
            </ul>
          @endif
          <p class="text-body-secondary mb-0">
            Biaya menyesuaikan merk dan kondisi motor, dikonfirmasi bengkel.
          </p>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-3">Status Reservasi</h6>
          <span class="badge bg-label-{{ $statusBadge }} mb-3">{{ $statusLabel }}</span>

          @if ($reservasi->servis)
            <div class="border rounded p-3 mt-3">
              <div class="small text-body-secondary">Status Servis</div>
              <div class="fw-semibold">{{ ucfirst($reservasi->servis->status->value) }}</div>
              @if ($reservasi->servis->mekanik)
                <div class="small text-body-secondary mt-1">Mekanik: {{ $reservasi->servis->mekanik->name }}</div>
              @endif
            </div>
          @endif

          @can('cancel', $reservasi)
            <form
              method="POST"
              action="{{ route('reservasi.cancel', $reservasi) }}"
              class="mt-4"
              onsubmit="return confirm('Batalkan reservasi {{ $reservasi->kode }}?')">
              @csrf
              <button type="submit" class="btn btn-outline-danger w-100">
                <i class="icon-base bx bx-x-circle me-1" aria-hidden="true"></i>Batalkan Reservasi
              </button>
            </form>
          @endcan
        </div>
      </div>
    </div>
  </div>
@endsection
