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

  @include('customer.partials.page-header', [
    'title' => 'Detail Reservasi',
    'subtitle' => 'Kode Booking '.$reservasi->kode,
    'backRoute' => route('booking-saya'),
    'backLabel' => 'Kembali ke Booking Saya',
  ])

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
        <x-ui.card-header
          title="Informasi Reservasi"
          subtitle="Ringkasan jadwal dan kendaraan"
          icon="bx bx-calendar-check"
          color="primary">
          <x-slot:actions>
            <span class="badge bg-label-{{ $statusBadge }}">{{ $statusLabel }}</span>
          </x-slot:actions>
        </x-ui.card-header>
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
        <x-ui.card-header
          title="Layanan"
          subtitle="Layanan yang dipilih saat reservasi"
          icon="bx bx-wrench"
          color="info" />
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

      @if ($reservasi->servis)
        <div class="card mt-4">
          <x-ui.card-header
            title="Progres Servis"
            subtitle="Posisi kendaraan Anda saat ini."
            icon="bx bx-wrench"
            color="primary">
            <x-slot:actions>
              <span class="badge bg-label-{{ $reservasi->servis->status->badge() }}">
                {{ $reservasi->servis->status->label() }}
              </span>
            </x-slot:actions>
          </x-ui.card-header>
          <div class="card-body">
            @include('partials.service-timeline', ['servis' => $reservasi->servis])

            <div class="row g-3 mt-4">
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Odometer saat masuk</div>
                <div class="fw-semibold">
                  {{ $reservasi->servis->odometer_awal !== null
                    ? number_format($reservasi->servis->odometer_awal, 0, ',', '.').' km'
                    : '-' }}
                </div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Odometer akhir</div>
                <div class="fw-semibold">
                  {{ $reservasi->servis->odometer !== null
                    ? number_format($reservasi->servis->odometer, 0, ',', '.').' km'
                    : '-' }}
                </div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Mulai dikerjakan</div>
                <div class="fw-semibold">{{ $reservasi->servis->waktu_mulai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Selesai dikerjakan</div>
                <div class="fw-semibold">{{ $reservasi->servis->waktu_selesai?->translatedFormat('d M Y H:i') ?? '-' }}</div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Rincian pekerjaan</div>
                <div class="text-break" style="white-space: pre-line">
                  {{ $reservasi->servis->rincian_pekerjaan ?: '-' }}
                </div>
              </div>
              <div class="col-12 col-sm-6">
                <div class="small text-body-secondary">Catatan</div>
                <div class="text-break" style="white-space: pre-line">
                  {{ $reservasi->servis->catatan ?: '-' }}
                </div>
              </div>
            </div>

            <a href="{{ route('riwayat-servis.index', ['kendaraan' => $reservasi->kendaraan_id]) }}" class="btn btn-sm btn-outline-primary mt-4">
              <i class="icon-base bx bx-history me-1" aria-hidden="true"></i>Lihat Riwayat Kendaraan
            </a>
          </div>
        </div>
      @endif
    </div>

    <div class="col-12 col-lg-4">
      <div class="card">
        <x-ui.card-header
          title="Status Reservasi"
          subtitle="Status dan tindakan"
          icon="bx bx-info-circle"
          color="secondary" />
        <div class="card-body">
          <span class="badge bg-label-{{ $statusBadge }} mb-3">{{ $statusLabel }}</span>

          @if ($reservasi->servis)
            <div class="border rounded p-3 mt-3">
              <div class="small text-body-secondary">Status Servis</div>
              <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-label-{{ $reservasi->servis->status->badge() }}">
                  {{ $reservasi->servis->status->label() }}
                </span>
              </div>
              @if ($reservasi->servis->mekanik)
                <div class="small text-body-secondary mt-2">Mekanik: {{ $reservasi->servis->mekanik->name }}</div>
              @endif
              <a href="{{ route('riwayat-servis.index', ['kendaraan' => $reservasi->kendaraan_id]) }}" class="small text-decoration-none d-inline-block mt-2">
                Lihat riwayat kendaraan
              </a>
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
