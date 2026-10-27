@extends('customer.layouts.app')

@section('title', 'Tambah Kendaraan')
@section('activeMenu', 'kendaraan')

@section('content')
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
    <div>
      <h4 class="mb-1">Tambah Kendaraan</h4>
      <p class="text-body-secondary mb-0">Daftarkan motor yang akan digunakan untuk reservasi servis.</p>
    </div>
    <a href="{{ route('kendaraan.index') }}" class="btn btn-outline-secondary">
      <i class="icon-base bx bx-arrow-back me-1" aria-hidden="true"></i>Kembali
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('kendaraan.store') }}">
        @csrf
        @include('customer.kendaraan._form', ['kendaraan' => null])

        <div class="d-flex flex-wrap gap-2 mt-6">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan Kendaraan
          </button>
          <a href="{{ route('kendaraan.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
