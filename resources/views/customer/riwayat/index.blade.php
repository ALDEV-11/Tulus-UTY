@extends('customer.layouts.app')

@section('title', 'Riwayat Servis')
@section('activeMenu', 'riwayat')

@section('content')
  @php
    $kendaraanTerpilih = $kendaraanId ? $kendaraans->firstWhere('id', $kendaraanId) : null;
  @endphp

  @include('customer.partials.page-header', [
    'title' => 'Riwayat Servis',
    'subtitle' => $kendaraanTerpilih
      ? 'Riwayat servis untuk '.$kendaraanTerpilih->plat_nomor.'.'
      : 'Seluruh riwayat servis kendaraan Anda.',
    'backRoute' => route('booking-saya'),
    'backLabel' => 'Kembali ke Booking Saya',
  ])

  <div class="card app-table-card">
    <x-ui.card-header
      title="Daftar Riwayat Servis"
      :subtitle="$riwayat->total().' servis tercatat'.($kendaraanTerpilih ? ' untuk kendaraan ini' : '').'.'"
      icon="bx bx-history"
      color="primary">
      <x-slot:actions>
        <a href="{{ route('riwayat-servis.index') }}"
           class="btn btn-sm btn-outline-secondary {{ $kendaraanId ? '' : 'd-none' }}">
          <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset
        </a>
      </x-slot:actions>

      <x-slot:toolbar>
        <form method="GET" action="{{ route('riwayat-servis.index') }}" class="row g-2 align-items-end">
          <div class="col-12 col-lg-6">
            <label class="form-label" for="kendaraan">Pilih kendaraan</label>
            <select class="form-select" id="kendaraan" name="kendaraan">
              <option value="">Semua kendaraan</option>
              @foreach ($kendaraans as $kendaraan)
                <option value="{{ $kendaraan->id }}" @selected((int) $kendaraanId === $kendaraan->id)>
                  {{ $kendaraan->plat_nomor }} — {{ $kendaraan->merk?->nama }} {{ $kendaraan->model }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-sm-6 col-lg-2">
            <button type="submit" class="btn btn-primary w-100">
              <i class="icon-base bx bx-filter-alt me-1" aria-hidden="true"></i>Tampilkan
            </button>
          </div>
        </form>
      </x-slot:toolbar>
    </x-ui.card-header>

    @if ($kendaraans->isEmpty())
      <div class="card-body">
        <x-ui.empty-state
          title="Belum ada kendaraan"
          description="Tambahkan kendaraan terlebih dahulu untuk melihat riwayat servis."
          icon="bx bx-motorcycle"
          color="primary">
          <x-slot:action>
            <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
              <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
            </a>
          </x-slot:action>
        </x-ui.empty-state>
      </div>
    @elseif ($riwayat->isEmpty())
      <div class="card-body">
        <x-ui.empty-state
          title="Belum ada riwayat servis"
          :description="$kendaraanId
            ? 'Kendaraan ini belum memiliki riwayat servis.'
            : 'Riwayat servis akan muncul setelah kendaraan Anda check in di bengkel.'"
          icon="bx bx-history"
          color="secondary">
          @if ($kendaraanId)
            <x-slot:action>
              <a href="{{ route('riwayat-servis.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Tampilkan Semua
              </a>
            </x-slot:action>
          @endif
        </x-ui.empty-state>
      </div>
    @else
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <caption class="visually-hidden">Riwayat servis kendaraan Anda beserta layanan, pekerjaan, odometer, catatan, dan statusnya</caption>
          <thead class="table-light">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Kendaraan</th>
              <th scope="col" class="d-none d-lg-table-cell">Layanan</th>
              <th scope="col">Rincian Pekerjaan</th>
              <th scope="col" class="d-none d-md-table-cell">Odometer</th>
              <th scope="col" class="d-none d-xl-table-cell">Catatan</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach ($riwayat as $servis)
              @php
                $layananServis = $servis->reservasi?->layanan->pluck('nama')->join(', ');
                $rincian = $servis->rincian_pekerjaan ?: $servis->tindakan;
                $catatan = $servis->catatan ?: $servis->diagnosa;
              @endphp
              <tr>
                <td class="text-nowrap">
                  <span class="fw-semibold">{{ $servis->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</span>
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
                  <span class="d-inline-block text-truncate" style="max-width: 15rem" title="{{ $layananServis ?: 'Belum ada layanan' }}">
                    {{ $layananServis ?: '-' }}
                  </span>
                </td>
                <td>
                  <span class="d-inline-block text-truncate" style="max-width: 20rem" title="{{ $rincian ?: 'Belum ada rincian pekerjaan' }}">
                    {{ $rincian ?: '-' }}
                  </span>
                </td>
                <td class="d-none d-md-table-cell text-nowrap">
                  {{ $servis->odometerTampil() !== null
                    ? number_format($servis->odometerTampil(), 0, ',', '.').' km'
                    : '-' }}
                </td>
                <td class="d-none d-xl-table-cell">
                  <span class="d-inline-block text-truncate" style="max-width: 16rem" title="{{ $catatan ?: 'Tidak ada catatan' }}">
                    {{ $catatan ?: '-' }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-label-{{ $servis->status->badge() }}">{{ $servis->status->label() }}</span>
                  <div class="mt-2" style="min-width: 8rem;">
                    @include('partials.service-timeline', ['servis' => $servis, 'compact' => true])
                  </div>
                </td>
                <td class="text-end text-nowrap">
                  @if ($servis->reservasi)
                    <a href="{{ route('reservasi.show', $servis->reservasi) }}" class="btn btn-sm btn-outline-primary">
                      Detail
                    </a>
                  @else
                    <span class="text-body-secondary small">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    @if ($riwayat->hasPages())
      <div class="card-footer">
        {{ $riwayat->links() }}
      </div>
    @endif
  </div>
@endsection
