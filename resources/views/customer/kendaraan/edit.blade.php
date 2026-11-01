@extends('customer.layouts.app')

@section('title', 'Edit Kendaraan')
@section('activeMenu', 'kendaraan')

@section('content')
  @include('customer.partials.page-header', [
    'title' => 'Edit Kendaraan',
    'subtitle' => 'Perbarui data '.$kendaraan->plat_nomor.'.',
    'backRoute' => route('kendaraan.index'),
  ])

  <div class="row g-4">
    <div class="col-12 col-xl-8">
      <div class="card app-form-card">
        <x-ui.card-header
          :title="$kendaraan->plat_nomor"
          subtitle="Ubah informasi kendaraan di bawah"
          icon="bx bx-edit-alt"
          color="warning" />

        <div class="card-body">
          <form method="POST" action="{{ route('kendaraan.update', $kendaraan) }}">
            @csrf
            @method('PUT')
            @include('customer.kendaraan._form', ['kendaraan' => $kendaraan])

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base bx bx-save me-1" aria-hidden="true"></i>Simpan Perubahan
              </button>
              <a href="{{ route('kendaraan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
