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
        'dijadwalkan' => 'Dijadwalkan',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $statusServisBadge = [
        'dijadwalkan' => 'info',
        'dikerjakan' => 'warning',
        'selesai' => 'success',
        'dibatalkan' => 'danger',
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

<!doctype html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-menu-fixed layout-compact"
  data-assets-path="{{ url('dist-dashboard/assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />

    <title>Booking Saya | {{ config('app.name') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('dist-dashboard/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset("dist-dashboard/assets/vendor/fonts/iconify-icons.css") }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset("dist-dashboard/assets/vendor/css/core.css") }}" />
    <link rel="stylesheet" href="{{ asset("dist-dashboard/assets/css/demo.css") }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset("dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css") }}" />

    <!-- endbuild -->

    @include('customer.partials.sidebar-style')

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset("dist-dashboard/assets/vendor/js/helpers.js") }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset("dist-dashboard/assets/js/config.js") }}"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

       @include('customer.partials.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

         @include('customer.partials.navbar')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="status">
                  {{ session('status') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
              @endif

              <div class="row g-6 mb-6">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                      <div>
                        <h4 class="mb-1">Halo, {{ auth()->user()->name }}!</h4>
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
                  </div>
                </div>
              </div>

              <div class="row g-6 mb-6">
                <div class="col-6 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                          <p class="text-body-secondary mb-1">Reservasi Aktif</p>
                          <h4 class="mb-0">{{ number_format($ringkasan['reservasi_aktif'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="avatar avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-warning">
                            <i class="icon-base bx bx-calendar-check" aria-hidden="true"></i>
                          </span>
                        </span>
                      </div>
                      <small class="text-body-secondary">Menunggu konfirmasi, dikonfirmasi, atau check in.</small>
                    </div>
                  </div>
                </div>

                <div class="col-6 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                          <p class="text-body-secondary mb-1">Servis Selesai</p>
                          <h4 class="mb-0">{{ number_format($ringkasan['servis_selesai'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="avatar avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="icon-base bx bx-check-circle" aria-hidden="true"></i>
                          </span>
                        </span>
                      </div>
                      <small class="text-body-secondary">Servis kendaraan Anda yang sudah selesai.</small>
                    </div>
                  </div>
                </div>

                <div class="col-6 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                          <p class="text-body-secondary mb-1">Kendaraan Saya</p>
                          <h4 class="mb-0">{{ number_format($ringkasan['kendaraan'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="avatar avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="icon-base bx bx-cycling" aria-hidden="true"></i>
                          </span>
                        </span>
                      </div>
                      <a href="{{ route('kendaraan.index') }}" class="small text-decoration-none">Kelola kendaraan</a>
                    </div>
                  </div>
                </div>

                <div class="col-6 col-xl-3">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                          <p class="text-body-secondary mb-1">Total Biaya Servis</p>
                          <h4 class="mb-0">Rp {{ number_format($ringkasan['total_biaya_servis'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="avatar avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-info">
                            <i class="icon-base bx bx-receipt" aria-hidden="true"></i>
                          </span>
                        </span>
                      </div>
                      <small class="text-body-secondary">Akumulasi biaya servis kendaraan Anda.</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-6" id="reservasi-saya">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                      <div>
                        <h5 class="mb-1">Reservasi Saya</h5>
                        <p class="card-subtitle mb-0">Pantau status reservasi servis motor Anda.</p>
                      </div>
                      <button
                        type="button"
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalBuatReservasi"
                        @disabled(! $canBook)>
                        <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Buat Reservasi
                      </button>
                    </div>
                    <div class="card-body">
                      @if (! $canBook)
                        <div class="alert alert-warning d-flex align-items-start gap-2" role="status">
                          <i class="icon-base bx bx-info-circle mt-1" aria-hidden="true"></i>
                          <div>
                            Reservasi belum dapat dibuat karena belum ada layanan aktif atau slot jadwal yang tersedia.
                            Silakan hubungi admin bengkel.
                          </div>
                        </div>
                      @endif

                      @if ($reservasiSaya->isEmpty())
                        <div class="text-center text-body-secondary py-6">
                          <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                              <i class="icon-base bx bx-calendar-plus icon-lg" aria-hidden="true"></i>
                            </span>
                          </div>
                          <h6 class="mb-1">Belum ada reservasi</h6>
                          <p class="mb-3">Buat reservasi pertama Anda untuk mulai servis motor.</p>
                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalBuatReservasi"
                            @disabled(! $canBook)>
                            <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Buat Reservasi
                          </button>
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
                          <div class="mt-4">
                            {{ $reservasiSaya->links() }}
                          </div>
                        @endif
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              <div class="row g-6">
                <div class="col-12 col-xl-8">
                  <div class="card h-100">
                    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                      <div>
                        <h5 class="mb-1">Servis Terakhir</h5>
                        <p class="card-subtitle mb-0">Riwayat servis kendaraan Anda.</p>
                      </div>
                      <a href="{{ route('kendaraan.index') }}" class="btn btn-sm btn-outline-primary">Kendaraan Saya</a>
                    </div>
                    <div class="card-body">
                      @if ($servisTerakhir->isEmpty())
                        <div class="text-center text-body-secondary py-6">
                          <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                              <i class="icon-base bx bx-wrench icon-lg" aria-hidden="true"></i>
                            </span>
                          </div>
                          <h6 class="mb-1">Belum ada riwayat servis</h6>
                          <p class="mb-0">Servis akan muncul di sini setelah reservasi Anda selesai dikerjakan.</p>
                        </div>
                      @else
                        <div class="table-responsive">
                          <table class="table align-middle mb-0">
                            <caption class="visually-hidden">Riwayat servis kendaraan Anda</caption>
                            <thead class="table-light">
                              <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Kendaraan</th>
                                <th scope="col" class="d-none d-md-table-cell">Tindakan</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Biaya</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              @foreach ($servisTerakhir as $servis)
                                @php
                                  $servisStatus = $servis->status->value;
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
                                  <td class="d-none d-md-table-cell">
                                    <span class="d-inline-block text-truncate" style="max-width: 18rem" title="{{ $servis->tindakan ?: 'Belum ada tindakan' }}">
                                      {{ $servis->tindakan ?: 'Belum ada tindakan' }}
                                    </span>
                                    @if ($servis->mekanik)
                                      <br><small class="text-body-secondary">Mekanik: {{ $servis->mekanik->name }}</small>
                                    @endif
                                  </td>
                                  <td>
                                    <span class="badge bg-label-{{ $statusServisBadge[$servisStatus] ?? 'secondary' }}">
                                      {{ $statusServisLabel[$servisStatus] ?? $servisStatus }}
                                    </span>
                                  </td>
                                  <td class="text-end text-nowrap">
                                    Rp {{ number_format((float) $servis->total_biaya, 0, ',', '.') }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="col-12 col-xl-4">
                  <div class="card h-100">
                    <div class="card-header">
                      <h5 class="mb-1">Slot Terdekat</h5>
                      <p class="card-subtitle mb-0">Jadwal servis yang masih tersedia.</p>
                    </div>
                    <div class="card-body">
                      @if ($slotTerdekat->isEmpty())
                        <div class="text-center text-body-secondary py-6">
                          <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                              <i class="icon-base bx bx-calendar-x icon-lg" aria-hidden="true"></i>
                            </span>
                          </div>
                          <h6 class="mb-1">Belum ada slot tersedia</h6>
                          <p class="mb-0">Silakan cek kembali beberapa saat lagi.</p>
                        </div>
                      @else
                        <ul class="list-unstyled mb-0">
                          @foreach ($slotTerdekat as $slot)
                            <li class="d-flex align-items-center justify-content-between gap-3 {{ $loop->last ? '' : 'border-bottom' }} py-3">
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

              <div class="row g-6 mt-0">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                      <div>
                        <h5 class="mb-1">Kendaraan Saya</h5>
                        <p class="card-subtitle mb-0">Data kendaraan yang terdaftar untuk servis.</p>
                      </div>
                      <a href="{{ route('kendaraan.create') }}" class="btn btn-sm btn-primary">
                        <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
                      </a>
                    </div>
                    <div class="card-body">
                      @if ($kendaraans->isEmpty())
                        <div class="text-center text-body-secondary py-6">
                          <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                              <i class="icon-base bx bx-cycling icon-lg" aria-hidden="true"></i>
                            </span>
                          </div>
                          <h6 class="mb-1">Belum ada kendaraan</h6>
                          <p class="mb-3">Tambahkan kendaraan agar dapat membuat reservasi servis.</p>
                          <a href="{{ route('kendaraan.create') }}" class="btn btn-sm btn-primary">
                            <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
                          </a>
                        </div>
                      @else
                        <div class="row g-4">
                          @foreach ($kendaraans as $kendaraan)
                            <div class="col-12 col-md-6 col-xl-4">
                              <div class="border rounded p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                  <div>
                                    <h6 class="mb-1">{{ $kendaraan->plat_nomor }}</h6>
                                    <p class="text-body-secondary mb-0">
                                      {{ trim(($kendaraan->merk?->nama ?? '').' '.($kendaraan->model ?? '')) ?: '-' }}
                                    </p>
                                  </div>
                                  <span class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                      <i class="icon-base bx bx-cycling" aria-hidden="true"></i>
                                    </span>
                                  </span>
                                </div>
                                <div class="d-flex flex-wrap gap-3 mt-3 small text-body-secondary">
                                  <span>
                                    <i class="icon-base bx bx-calendar me-1" aria-hidden="true"></i>
                                    {{ $kendaraan->tahun ?? '-' }}
                                  </span>
                                  <span>
                                    <i class="icon-base bx bx-tachometer me-1" aria-hidden="true"></i>
                                    {{ number_format($kendaraan->kilometer, 0, ',', '.') }} km
                                  </span>
                                </div>
                                <a href="{{ route('kendaraan.edit', $kendaraan) }}" class="btn btn-sm btn-outline-primary mt-3">Edit</a>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
           @include('customer.partials.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <div class="modal fade" id="modalBuatReservasi" tabindex="-1" aria-labelledby="modalBuatReservasiLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <form method="POST" action="{{ route('reservasi.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="modalBuatReservasi">

            <div class="modal-header">
              <div>
                <h5 class="modal-title mb-1" id="modalBuatReservasiLabel">Buat Reservasi</h5>
                <small class="text-body-secondary">Pilih kendaraan, jadwal, dan layanan servis.</small>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
              @if (! $canBook)
                <div class="alert alert-warning" role="alert">
                  <i class="icon-base bx bx-error-circle me-1" aria-hidden="true"></i>
                  Reservasi belum dapat dibuat karena belum ada layanan aktif atau slot jadwal yang tersedia.
                  Silakan hubungi admin bengkel.
                </div>
              @endif

              @if ($requestedSlotUnavailable)
                <div class="alert alert-warning" role="alert">
                  <i class="icon-base bx bx-error-circle me-1" aria-hidden="true"></i>
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
                          <div class="form-check border rounded p-3 ps-5">
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
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" @disabled(! $canBook)>
                <i class="icon-base bx bx-calendar-check me-1" aria-hidden="true"></i>Kirim Reservasi
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Core JS -->

    <script src="{{ asset("dist-dashboard/assets/vendor/libs/jquery/jquery.js") }}"></script>

    <script src="{{ asset("dist-dashboard/assets/vendor/libs/popper/popper.js") }}"></script>
    <script src="{{ asset("dist-dashboard/assets/vendor/js/bootstrap.js") }}"></script>

    <script src="{{ asset("dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js") }}"></script>

    <script src="{{ asset("dist-dashboard/assets/vendor/js/menu.js") }}"></script>

    <!-- endbuild -->

    <!-- Main JS -->

    <script src="{{ asset("dist-dashboard/assets/js/main.js") }}"></script>

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

  </body>
</html>
