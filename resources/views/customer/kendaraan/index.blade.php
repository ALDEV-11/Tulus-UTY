@extends('customer.layouts.app')

@section('title', 'Kendaraan Saya')
@section('activeMenu', 'kendaraan')

@section('content')
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
    <div>
      <h4 class="mb-1">Kendaraan Saya</h4>
      <p class="text-body-secondary mb-0">Kelola motor yang terdaftar untuk reservasi servis.</p>
    </div>
    <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
      <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
    </a>
  </div>

  @if ($kendaraans->isEmpty())
    <div class="card">
      <div class="card-body text-center py-6">
        <div class="avatar avatar-lg mb-3">
          <span class="avatar-initial rounded-circle bg-label-primary">
            <i class="icon-base bx bx-cycling icon-lg" aria-hidden="true"></i>
          </span>
        </div>
        <h5 class="mb-1">Belum ada kendaraan</h5>
        <p class="text-body-secondary mb-4">Tambahkan motor Anda agar bisa membuat reservasi servis.</p>
        <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
          <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
        </a>
      </div>
    </div>
  @else
    <div class="row g-4">
      @foreach ($kendaraans as $kendaraan)
        <div class="col-12 col-lg-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                  <h5 class="mb-1">{{ $kendaraan->plat_nomor }}</h5>
                  <p class="text-body-secondary mb-0">
                    {{ trim(($kendaraan->merk?->nama ?? '').' '.($kendaraan->model ?? '')) ?: 'Tipe belum diisi' }}
                  </p>
                </div>
                <span class="badge bg-label-primary">{{ $kendaraan->tahun ?: '-' }}</span>
              </div>

              <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-label-secondary">{{ $kendaraan->reservasi_count }} reservasi</span>
                <span class="badge bg-label-secondary">{{ $kendaraan->servis_count }} servis</span>
              </div>

              <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('kendaraan.edit', $kendaraan) }}" class="btn btn-sm btn-outline-primary">
                  <i class="icon-base bx bx-edit-alt me-1" aria-hidden="true"></i>Edit
                </a>
                <form
                  method="POST"
                  action="{{ route('kendaraan.destroy', $kendaraan) }}"
                  onsubmit="return confirm('Hapus kendaraan {{ $kendaraan->plat_nomor }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="icon-base bx bx-trash me-1" aria-hidden="true"></i>Hapus
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
