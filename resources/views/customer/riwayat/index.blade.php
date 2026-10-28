@extends('customer.layouts.app')

@section('title', 'Riwayat Servis')
@section('activeMenu', 'riwayat')

@section('content')
  @php
    $kendaraanTerpilih = $kendaraanId ? $kendaraans->firstWhere('id', $kendaraanId) : null;
  @endphp

  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
    <div>
      <h4 class="mb-1">Riwayat Servis</h4>
      <p class="text-body-secondary mb-0">
        {{ $kendaraanTerpilih
          ? 'Riwayat servis untuk '.$kendaraanTerpilih->plat_nomor.'.'
          : 'Seluruh riwayat servis kendaraan Anda.' }}
      </p>
    </div>
    <a href="{{ route('booking-saya') }}" class="btn btn-outline-secondary">
      <i class="icon-base bx bx-arrow-back me-1" aria-hidden="true"></i>Kembali ke Booking Saya
    </a>
  </div>

  <div class="card mb-6">
    <div class="card-body">
      <form method="GET" action="{{ route('riwayat-servis.index') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-6 col-lg-4">
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
        <div class="col-12 col-md-6 col-lg-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">
            <i class="icon-base bx bx-filter-alt me-1" aria-hidden="true"></i>Tampilkan
          </button>
          @if ($kendaraanId)
            <a href="{{ route('riwayat-servis.index') }}" class="btn btn-outline-secondary">
              <i class="icon-base bx bx-reset me-1" aria-hidden="true"></i>Reset
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-1">Daftar Riwayat Servis</h5>
      <p class="card-subtitle mb-0">
        {{ $riwayat->total() }} servis tercatat{{ $kendaraanTerpilih ? ' untuk kendaraan ini' : '' }}.
      </p>
    </div>

    <div class="card-body">
      @if ($kendaraans->isEmpty())
        <div class="text-center text-body-secondary py-6">
          <div class="avatar avatar-lg mb-3">
            <span class="avatar-initial rounded-circle bg-label-primary">
              <i class="icon-base bx bx-cycling icon-lg" aria-hidden="true"></i>
            </span>
          </div>
          <h5 class="mb-1">Belum ada kendaraan</h5>
          <p class="text-body-secondary mb-4">Tambahkan kendaraan terlebih dahulu untuk melihat riwayat servis.</p>
          <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
            <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
          </a>
        </div>
      @elseif ($riwayat->isEmpty())
        <div class="text-center text-body-secondary py-6">
          <div class="avatar avatar-lg mb-3">
            <span class="avatar-initial rounded-circle bg-label-secondary">
              <i class="icon-base bx bx-wrench icon-lg" aria-hidden="true"></i>
            </span>
          </div>
          <h5 class="mb-1">Belum ada riwayat servis</h5>
          <p class="mb-0">
            {{ $kendaraanId
              ? 'Kendaraan ini belum memiliki riwayat servis.'
              : 'Riwayat servis akan muncul setelah kendaraan Anda check in di bengkel.' }}
          </p>
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
    </div>

    @if ($riwayat->hasPages())
      <div class="card-footer">
        {{ $riwayat->links() }}
      </div>
    @endif
  </div>
@endsection
