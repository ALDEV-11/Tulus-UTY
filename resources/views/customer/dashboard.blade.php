@extends('customer.layouts.app')

@section('title', 'Booking Saya')
@section('activeMenu', 'booking')

@php
    $statusReservasiLabel = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'dikonfirmasi' => 'Dikonfirmasi',
        'check_in' => 'Check In',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $statusReservasiBadge = [
        'menunggu_konfirmasi' => 'warning',
        'dikonfirmasi' => 'primary',
        'check_in' => 'info',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];
    $statusServisLabel = [
        'menunggu' => 'Menunggu',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'diambil' => 'Diambil',
    ];
    $statusServisBadge = [
        'menunggu' => 'warning',
        'dikerjakan' => 'info',
        'selesai' => 'success',
        'diambil' => 'primary',
    ];
    $canBook = $availableSlots->isNotEmpty() && $layanans->isNotEmpty();
    $oldLayanan = array_map('intval', (array) old('layanan', []));
    $selectedSlotId = old('id_slot', $requestedSlotId ?? null);
    $oldKendaraanBaru = old('kendaraan_baru', []);
    $pakaiKendaraanBaru = $kendaraans->isEmpty()
        || $errors->has('kendaraan_baru')
        || $errors->has('kendaraan_baru.merk_motor_id')
        || $errors->has('kendaraan_baru.plat_nomor')
        || $errors->has('kendaraan_baru.model')
        || $errors->has('kendaraan_baru.tahun')
        || (is_array($oldKendaraanBaru) && count(array_filter(
            $oldKendaraanBaru,
            fn ($value) => $value !== null && $value !== ''
        )) > 0);
@endphp

@push('styles')
<style>
    .dashboard-stat-card .avatar-initial {
        width: 3rem;
        height: 3rem;
        font-size: 1.35rem;
    }

    .dashboard-list > :last-child {
        border-bottom: 0 !important;
    }
</style>
@endpush

@section('content')
  <div class="app-page-header mb-6">
    <div>
      <h4 class="fw-bold mb-1">Halo, {{ auth()->user()->name }}!</h4>
      <p class="text-body-secondary mb-0">
        Pantau reservasi, servis, dan kendaraan Anda dalam satu tempat.
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalBuatReservasi"
        @disabled(! $canBook)>
        <i class="icon-base bx bx-calendar-plus me-1" aria-hidden="true"></i>Buat Reservasi
      </button>
      <a href="{{ route('slot.publik') }}" class="btn btn-outline-primary">
        <i class="icon-base bx bx-time-five me-1" aria-hidden="true"></i>Cek Slot
      </a>
    </div>
  </div>

  <div class="row g-4 mb-6">
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 dashboard-stat-card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <p class="text-body-secondary mb-1">Reservasi Aktif</p>
              <h3 class="mb-1">{{ number_format($ringkasan['reservasi_aktif'], 0, ',', '.') }}</h3>
              <small class="text-body-secondary">Menunggu konfirmasi, dikonfirmasi, atau check in.</small>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded-circle bg-label-warning">
                <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 dashboard-stat-card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <p class="text-body-secondary mb-1">Servis Berlangsung</p>
              <h3 class="mb-1">{{ number_format($ringkasan['servis_berlangsung'], 0, ',', '.') }}</h3>
              <small class="text-body-secondary">Kendaraan yang menunggu atau sedang dikerjakan.</small>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded-circle bg-label-info">
                <i class="icon-base bx bx-wrench" aria-hidden="true"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 dashboard-stat-card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <p class="text-body-secondary mb-1">Kendaraan Saya</p>
              <h3 class="mb-1">{{ number_format($ringkasan['kendaraan'], 0, ',', '.') }}</h3>
              <small>
                <a href="{{ route('kendaraan.index') }}" class="text-decoration-none">Kelola kendaraan</a>
              </small>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded-circle bg-label-primary">
                <i class="icon-base bx bx-motorcycle" aria-hidden="true"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card h-100 dashboard-stat-card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <p class="text-body-secondary mb-1">Riwayat Servis</p>
              <h3 class="mb-1">{{ number_format($ringkasan['servis_selesai'], 0, ',', '.') }}</h3>
              <small>
                <a href="{{ route('riwayat-servis.index') }}" class="text-decoration-none">Lihat riwayat servis</a>
              </small>
            </div>
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded-circle bg-label-success">
                <i class="icon-base bx bx-history" aria-hidden="true"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-6" id="reservasi-saya">
    <div class="col-12">
      <div class="card app-table-card">
        <x-ui.card-header
          title="Reservasi Saya"
          subtitle="Pantau status reservasi servis motor Anda."
          icon="bx bx-calendar-check"
          color="primary">
          <x-slot:actions>
            <button
              type="button"
              class="btn btn-sm btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#modalBuatReservasi"
              @disabled(! $canBook)>
              <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Buat Reservasi
            </button>
          </x-slot:actions>
        </x-ui.card-header>

        @if (! $canBook)
          <div class="card-body pb-0">
            <div class="alert alert-warning mb-0" role="status">
              Reservasi belum dapat dibuat karena belum ada layanan aktif atau slot jadwal yang tersedia.
              Silakan hubungi admin bengkel.
            </div>
          </div>
        @endif

        @if ($reservasiSaya->isEmpty())
          <div class="card-body">
            <x-ui.empty-state
              title="Belum ada reservasi"
              description="Buat reservasi pertama Anda untuk mulai servis motor."
              icon="bx bx-calendar-check"
              color="primary">
              <x-slot:action>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#modalBuatReservasi"
                  @disabled(! $canBook)>
                  <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Buat Reservasi
                </button>
              </x-slot:action>
            </x-ui.empty-state>
          </div>
        @else
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <caption class="visually-hidden">Daftar reservasi servis motor Anda beserta jadwal, kendaraan, layanan, dan statusnya</caption>
              <thead class="table-light">
                <tr>
                  <th scope="col">Kode</th>
                  <th scope="col">Jadwal</th>
                  <th scope="col">Kendaraan</th>
                  <th scope="col" class="d-none d-lg-table-cell">Layanan</th>
                  <th scope="col" class="text-center">Status</th>
                  <th scope="col" class="text-center">Progres Servis</th>
                  <th scope="col" class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @foreach ($reservasiSaya as $reservasi)
                  @php
                    $statusValue = $reservasi->status->value;
                  @endphp
                  <tr>
                    <td>
                      <span class="fw-semibold">{{ $reservasi->kode }}</span><br>
                      <small class="text-body-secondary">{{ $reservasi->created_at?->translatedFormat('d M Y') }}</small>
                    </td>
                    <td class="text-nowrap">
                      {{ $reservasi->slot?->tanggal?->translatedFormat('d M Y') ?? '-' }}<br>
                      <small class="text-body-secondary">
                        {{ $reservasi->slot ? substr($reservasi->slot->jam_mulai, 0, 5).' – '.substr($reservasi->slot->jam_selesai, 0, 5) : '-' }}
                      </small>
                    </td>
                    <td>
                      {{ $reservasi->kendaraan?->plat_nomor ?? '-' }}<br>
                      <small class="text-body-secondary">
                        {{ trim(($reservasi->kendaraan?->merk?->nama ?? '').' '.($reservasi->kendaraan?->model ?? '')) ?: '-' }}
                      </small>
                    </td>
                    <td class="d-none d-lg-table-cell">
                      <span class="d-inline-block text-truncate" style="max-width: 14rem" title="{{ $reservasi->layanan->pluck('nama')->join(', ') ?: 'Belum ada layanan' }}">
                        {{ $reservasi->layanan->pluck('nama')->join(', ') ?: 'Belum ada layanan' }}
                      </span>
                    </td>
                    @php
                      $alasanPenolakan = $reservasi->alasanPenolakan();
                      $labelStatus = $alasanPenolakan ? 'Ditolak' : ($statusReservasiLabel[$statusValue] ?? $statusValue);
                      $badgeStatus = $alasanPenolakan ? 'danger' : ($statusReservasiBadge[$statusValue] ?? 'secondary');
                    @endphp
                    <td class="text-center">
                      <span class="badge bg-label-{{ $badgeStatus }}">{{ $labelStatus }}</span>
                      @if ($alasanPenolakan)
                        <div class="small text-danger mt-1" style="max-width: 14rem">{{ $alasanPenolakan }}</div>
                      @endif
                    </td>
                    <td class="text-center" style="min-width: 10rem;">
                      @if ($reservasi->servis)
                        @include('partials.service-timeline', ['servis' => $reservasi->servis, 'compact' => true])
                      @else
                        <span class="small text-body-secondary">Belum check in</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <div class="d-flex flex-column flex-sm-row gap-1 justify-content-end">
                        <a href="{{ route('reservasi.show', $reservasi) }}" class="btn btn-sm btn-outline-primary text-nowrap">
                          Detail
                        </a>
                        @if (in_array($statusValue, ['menunggu_konfirmasi', 'dikonfirmasi'], true))
                          <form
                            method="POST"
                            action="{{ route('reservasi.cancel', $reservasi) }}"
                            onsubmit="return confirm('Batalkan reservasi {{ $reservasi->kode }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger text-nowrap">
                              Batalkan
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @if ($reservasiSaya->hasPages())
            <div class="card-footer">
              {{ $reservasiSaya->links() }}
            </div>
          @endif
        @endif
      </div>
    </div>
  </div>

  <div class="row g-4 mb-6">
    <div class="col-12 col-xl-8">
      <div class="card h-100 app-table-card">
        <x-ui.card-header
          title="Servis Terakhir"
          subtitle="Riwayat servis kendaraan Anda."
          icon="bx bx-history"
          color="success">
          <x-slot:actions>
            <a href="{{ route('riwayat-servis.index') }}" class="btn btn-sm btn-outline-primary">Riwayat Servis</a>
          </x-slot:actions>
        </x-ui.card-header>

        @if ($servisTerakhir->isEmpty())
          <div class="card-body">
            <x-ui.empty-state
              title="Belum ada riwayat servis"
              description="Servis akan muncul di sini setelah kendaraan Anda check in."
              icon="bx bx-history"
              color="secondary" />
          </div>
        @else
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <caption class="visually-hidden">Riwayat servis kendaraan Anda</caption>
              <thead class="table-light">
                <tr>
                  <th scope="col">Tanggal</th>
                  <th scope="col">Kendaraan</th>
                  <th scope="col" class="d-none d-lg-table-cell">Layanan</th>
                  <th scope="col" class="d-none d-md-table-cell">Pekerjaan</th>
                  <th scope="col" class="d-none d-lg-table-cell">Odometer</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @foreach ($servisTerakhir as $servis)
                  @php
                    $servisStatus = $servis->status->value;
                    $layananServis = $servis->reservasi?->layanan->pluck('nama')->join(', ');
                  @endphp
                  <tr>
                    <td class="text-nowrap">
                      {{ $servis->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}
                      @if ($servis->tanggal_selesai)
                        <br><small class="text-body-secondary">Selesai {{ $servis->tanggal_selesai->translatedFormat('d M Y') }}</small>
                      @endif
                    </td>
                    <td>
                      {{ $servis->kendaraan?->plat_nomor ?? '-' }}<br>
                      <small class="text-body-secondary">
                        {{ trim(($servis->kendaraan?->merk?->nama ?? '').' '.($servis->kendaraan?->model ?? '')) ?: '-' }}
                      </small>
                    </td>
                    <td class="d-none d-lg-table-cell">
                      <span class="d-inline-block text-truncate" style="max-width: 14rem" title="{{ $layananServis ?: 'Belum ada layanan' }}">
                        {{ $layananServis ?: '-' }}
                      </span>
                    </td>
                    <td class="d-none d-md-table-cell">
                      <span class="d-inline-block text-truncate" style="max-width: 18rem" title="{{ $servis->rincian_pekerjaan ?: 'Belum ada rincian pekerjaan' }}">
                        {{ $servis->rincian_pekerjaan ?: '-' }}
                      </span>
                    </td>
                    <td class="d-none d-lg-table-cell text-nowrap">
                      {{ $servis->odometerTampil() !== null ? number_format($servis->odometerTampil(), 0, ',', '.').' km' : '-' }}
                    </td>
                    <td>
                      <span class="badge bg-label-{{ $statusServisBadge[$servisStatus] ?? 'secondary' }}">
                        {{ $statusServisLabel[$servisStatus] ?? $servisStatus }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card h-100">
        <x-ui.card-header
          title="Slot Terdekat"
          subtitle="Jadwal servis yang masih tersedia."
          icon="bx bx-time-five"
          color="warning" />

        <div class="card-body">
          @if ($slotTerdekat->isEmpty())
            <x-ui.empty-state
              title="Belum ada slot tersedia"
              description="Silakan cek kembali beberapa saat lagi."
              icon="bx bx-calendar-x"
              color="secondary" />
          @else
            <ul class="list-unstyled dashboard-list mb-0">
              @foreach ($slotTerdekat as $slot)
                <li class="d-flex align-items-center justify-content-between gap-3 border-bottom py-3">
                  <div>
                    <div class="fw-semibold">{{ $slot->tanggal->translatedFormat('d M Y') }}</div>
                    <small class="text-body-secondary">
                      {{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}
                      &middot; sisa {{ $slot->sisa }} dari {{ $slot->kapasitas }}
                    </small>
                  </div>
                  <a
                    href="{{ route('booking-saya', ['slot' => $slot->id, 'tanggal' => $slot->tanggal->toDateString()]) }}"
                    class="btn btn-sm btn-outline-primary text-nowrap">
                    Pilih
                  </a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>

        <div class="card-footer">
          <a href="{{ route('slot.publik') }}" class="btn btn-sm btn-outline-primary w-100">Lihat Semua Slot</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12">
      <div class="card">
        <x-ui.card-header
          title="Kendaraan Saya"
          subtitle="Data kendaraan yang terdaftar untuk servis."
          icon="bx bx-motorcycle"
          color="primary">
          <x-slot:actions>
            <a href="{{ route('kendaraan.create') }}" class="btn btn-sm btn-primary">
              <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
            </a>
          </x-slot:actions>
        </x-ui.card-header>

        <div class="card-body">
          @if ($kendaraans->isEmpty())
            <x-ui.empty-state
              title="Belum ada kendaraan"
              description="Tambahkan kendaraan agar dapat membuat reservasi servis."
              icon="bx bx-motorcycle"
              color="primary">
              <x-slot:action>
                <a href="{{ route('kendaraan.create') }}" class="btn btn-sm btn-primary">
                  <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
                </a>
              </x-slot:action>
            </x-ui.empty-state>
          @else
            <div class="row g-4">
              @foreach ($kendaraans as $kendaraan)
                <div class="col-12 col-md-6 col-xl-4">
                  <div class="border rounded-3 p-4 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                      <span class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                          <i class="icon-base bx bx-motorcycle" aria-hidden="true"></i>
                        </span>
                      </span>
                      <div style="min-width: 0">
                        <h6 class="mb-0">{{ $kendaraan->plat_nomor }}</h6>
                        <small class="text-body-secondary">
                          {{ trim(($kendaraan->merk?->nama ?? '').' '.($kendaraan->model ?? '')) ?: '-' }}
                        </small>
                      </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 small text-body-secondary">
                      <span>{{ $kendaraan->tahun ?? '-' }}</span>
                      <span>{{ number_format($kendaraan->kilometer, 0, ',', '.') }} km</span>
                    </div>

                    <a href="{{ route('kendaraan.edit', $kendaraan) }}" class="btn btn-sm btn-outline-primary mt-4 align-self-start">Edit</a>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalBuatReservasi" tabindex="-1" aria-labelledby="modalBuatReservasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-1" id="modalBuatReservasiLabel">Buat Reservasi</h5>
            <small class="text-body-secondary">Pilih kendaraan, jadwal, dan layanan servis.</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <form method="POST" action="{{ route('reservasi.store') }}" id="formBuatReservasi">
            @csrf
            <input type="hidden" name="_modal" value="modalBuatReservasi">

            @if (! $canBook)
              <div class="alert alert-warning" role="alert">
                Reservasi belum dapat dibuat karena belum ada layanan aktif atau slot jadwal yang tersedia.
                Silakan hubungi admin bengkel.
              </div>
            @endif

            @if ($requestedSlotUnavailable)
              <div class="alert alert-warning" role="alert">
                Slot yang Anda pilih dari halaman cek slot sudah penuh atau tidak tersedia. Silakan pilih jadwal lain.
              </div>
            @endif

            <div class="row g-4">
              <div class="col-12">
                <label class="form-label" for="kendaraan_id">Kendaraan</label>
                <select
                  class="form-select @error('kendaraan_id') is-invalid @enderror"
                  id="kendaraan_id"
                  name="kendaraan_id"
                  @required(! $pakaiKendaraanBaru && $kendaraans->isNotEmpty())
                  @disabled($kendaraans->isEmpty())>
                  <option value="">Pilih kendaraan</option>
                  @foreach ($kendaraans as $kendaraan)
                    <option value="{{ $kendaraan->id }}" @selected((string) old('kendaraan_id') === (string) $kendaraan->id)>
                      {{ $kendaraan->plat_nomor }} — {{ $kendaraan->merk?->nama }} {{ $kendaraan->model }}
                    </option>
                  @endforeach
                </select>
                @error('kendaraan_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-check mt-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="tambah_kendaraan_baru"
                    @checked($pakaiKendaraanBaru)>
                  <label class="form-check-label" for="tambah_kendaraan_baru">
                    Tambah kendaraan baru
                  </label>
                </div>

                <div id="kendaraan-baru-fields" class="border rounded p-3 mt-3" @if (! $pakaiKendaraanBaru) hidden @endif>
                  <h6 class="mb-3">Data Kendaraan Baru</h6>
                  <div class="row g-3">
                    <div class="col-12 col-sm-6">
                      <label class="form-label" for="merk_motor_id_baru">Merk</label>
                      <select
                        class="form-select @error('kendaraan_baru.merk_motor_id') is-invalid @enderror"
                        id="merk_motor_id_baru"
                        name="kendaraan_baru[merk_motor_id]"
                        @required($pakaiKendaraanBaru)>
                        <option value="">Pilih merk</option>
                        @foreach ($merks as $merk)
                          <option value="{{ $merk->id }}" @selected((string) old('kendaraan_baru.merk_motor_id') === (string) $merk->id)>
                            {{ $merk->nama }}
                          </option>
                        @endforeach
                      </select>
                      @error('kendaraan_baru.merk_motor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                      <label class="form-label" for="plat_nomor_baru">Nomor Polisi</label>
                      <input
                        type="text"
                        class="form-control @error('kendaraan_baru.plat_nomor') is-invalid @enderror"
                        id="plat_nomor_baru"
                        name="kendaraan_baru[plat_nomor]"
                        value="{{ old('kendaraan_baru.plat_nomor') }}"
                        maxlength="15"
                        placeholder="Contoh: AB 1234 CD"
                        autocomplete="off"
                        @required($pakaiKendaraanBaru)>
                      @error('kendaraan_baru.plat_nomor')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                      <label class="form-label" for="model_baru">Tipe</label>
                      <input
                        type="text"
                        class="form-control @error('kendaraan_baru.model') is-invalid @enderror"
                        id="model_baru"
                        name="kendaraan_baru[model]"
                        value="{{ old('kendaraan_baru.model') }}"
                        maxlength="100"
                        placeholder="Contoh: Beat Street"
                        @required($pakaiKendaraanBaru)>
                      @error('kendaraan_baru.model')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                      <label class="form-label" for="tahun_baru">Tahun</label>
                      <input
                        type="number"
                        class="form-control @error('kendaraan_baru.tahun') is-invalid @enderror"
                        id="tahun_baru"
                        name="kendaraan_baru[tahun]"
                        value="{{ old('kendaraan_baru.tahun') }}"
                        min="1980"
                        max="{{ now()->year + 1 }}"
                        placeholder="Contoh: 2022"
                        @required($pakaiKendaraanBaru)>
                      @error('kendaraan_baru.tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label" for="id_slot">Jadwal</label>
                <select
                  class="form-select @error('id_slot') is-invalid @enderror"
                  id="id_slot"
                  name="id_slot"
                  required
                  @disabled($availableSlots->isEmpty())>
                  <option value="">Pilih tanggal dan jam</option>
                  @foreach ($availableSlots->groupBy(fn ($slot) => $slot->tanggal->toDateString()) as $tanggal => $slots)
                    <optgroup label="{{ $slots->first()->tanggal->translatedFormat('l, d M Y') }}">
                      @foreach ($slots as $slot)
                        <option value="{{ $slot->id }}" @selected((string) $selectedSlotId === (string) $slot->id)>
                          {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                          (sisa {{ $slot->sisa }} slot)
                        </option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select>
                @error('id_slot')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Hanya slot yang masih tersedia yang ditampilkan.</div>
              </div>

              <div class="col-12">
                <fieldset>
                  <legend class="form-label fs-6">Layanan</legend>
                  <div class="row g-2">
                    @foreach ($layanans as $layanan)
                      <div class="col-12 col-sm-6">
                        <div class="border rounded p-3">
                          <div class="form-check mb-0">
                            <input
                              class="form-check-input @error('layanan') is-invalid @enderror"
                              type="checkbox"
                              name="layanan[]"
                              value="{{ $layanan->id }}"
                              id="layanan-{{ $layanan->id }}"
                              @checked(in_array($layanan->id, $oldLayanan, true))>
                            <label class="form-check-label" for="layanan-{{ $layanan->id }}">
                              <span class="fw-semibold">{{ $layanan->nama }}</span><br>
                              <small class="text-body-secondary">{{ $layanan->durasi_menit }} menit</small>
                            </label>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  @error('layanan')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                  @error('layanan.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </fieldset>
              </div>

              <div class="col-12">
                <div class="alert alert-info py-2 px-3 small mb-0" role="status">
                  Biaya menyesuaikan merk dan kondisi motor, dikonfirmasi bengkel.
                </div>
              </div>

              <div class="col-12">
                <label class="form-label" for="keluhan">Keluhan (opsional)</label>
                <textarea
                  class="form-control @error('keluhan') is-invalid @enderror"
                  id="keluhan"
                  name="keluhan"
                  rows="3"
                  maxlength="1000"
                  placeholder="Contoh: motor berbunyi kasar saat digas">{{ old('keluhan') }}</textarea>
                @error('keluhan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" form="formBuatReservasi" class="btn btn-primary" @disabled(! $canBook)>
            <i class="icon-base bx bx-calendar-check me-1" aria-hidden="true"></i>Kirim Reservasi
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalId = @json(old('_modal'));
    const requestedSlotId = @json($requestedSlotId);
    const checkbox = document.getElementById('tambah_kendaraan_baru');
    const kendaraanSelect = document.getElementById('kendaraan_id');
    const kendaraanBaruFields = document.getElementById('kendaraan-baru-fields');
    const kendaraanBaruInputs = kendaraanBaruFields
      ? kendaraanBaruFields.querySelectorAll('select, input')
      : [];

    function syncKendaraanBaru() {
      if (!checkbox || !kendaraanSelect || !kendaraanBaruFields) {
        return;
      }

      const isNew = checkbox.checked;
      kendaraanBaruFields.hidden = !isNew;
      kendaraanSelect.disabled = isNew;
      kendaraanSelect.required = !isNew;
      kendaraanBaruInputs.forEach(function (input) {
        input.required = isNew;
      });
    }

    if (checkbox) {
      checkbox.addEventListener('change', syncKendaraanBaru);
      syncKendaraanBaru();
    }

    if ((modalId === 'modalBuatReservasi' || requestedSlotId !== null) && window.bootstrap) {
      const modalElement = document.getElementById('modalBuatReservasi');

      if (modalElement) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
      }
    }
  });
</script>
@endpush
