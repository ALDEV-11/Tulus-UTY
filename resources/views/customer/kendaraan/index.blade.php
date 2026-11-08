@extends('customer.layouts.app')

@section('title', 'Kendaraan Saya')
@section('activeMenu', 'kendaraan')

@section('content')
    @include('customer.partials.page-header', [
        'title' => 'Kendaraan Saya',
        'subtitle' => 'Kelola motor yang terdaftar untuk reservasi servis.',
        'actionRoute' => route('kendaraan.create'),
        'actionLabel' => 'Tambah Kendaraan',
    ])

    @if ($kendaraans->isEmpty())
        <div class="card">
            <x-ui.empty-state
                title="Belum ada kendaraan"
                description="Tambahkan motor Anda agar bisa membuat reservasi servis."
                icon="bx bx-motorcycle"
                color="primary">
                <x-slot:action>
                    <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
                        <i class="icon-base bx bx-plus me-1" aria-hidden="true"></i>Tambah Kendaraan
                    </a>
                </x-slot:action>
            </x-ui.empty-state>
        </div>
    @else
        <div class="row g-4">
            @foreach ($kendaraans as $kendaraan)
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <x-ui.card-header
                            :title="$kendaraan->plat_nomor"
                            :subtitle="trim(($kendaraan->merk?->nama ?? '').' '.($kendaraan->model ?? '')) ?: 'Tipe belum diisi'"
                            icon="bx bx-motorcycle"
                            color="primary">
                            <x-slot:actions>
                                <span class="badge bg-label-primary">{{ $kendaraan->tahun ?: '-' }}</span>
                            </x-slot:actions>
                        </x-ui.card-header>

                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <span class="badge bg-label-secondary">{{ $kendaraan->reservasi_count }} reservasi</span>
                                <span class="badge bg-label-secondary">{{ $kendaraan->servis_count }} servis</span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('riwayat-servis.index', ['kendaraan' => $kendaraan->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="icon-base bx bx-history me-1" aria-hidden="true"></i>Riwayat Servis
                                </a>
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
