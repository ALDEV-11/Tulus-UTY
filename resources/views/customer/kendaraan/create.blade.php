@extends('customer.layouts.app')

@section('title', 'Tambah Kendaraan')
@section('activeMenu', 'kendaraan')

@section('content')
  @include('customer.partials.page-header', [
    'title' => 'Tambah Kendaraan',
    'subtitle' => 'Daftarkan motor yang akan digunakan untuk reservasi servis.',
    'backRoute' => route('kendaraan.index'),
  ])

  <div class="row g-4">
    <div class="col-12 col-xl-8">
      <div class="card app-form-card">
        <x-ui.card-header
          title="Informasi Kendaraan"
          subtitle="Isi data motor dengan lengkap"
          icon="bx bx-motorcycle"
          color="primary" />

        <div class="card-body">
          <form method="POST" action="{{ route('kendaraan.store') }}">
            @csrf
            @include('customer.kendaraan._form', ['kendaraan' => null])

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan Kendaraan
              </button>
              <a href="{{ route('kendaraan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
