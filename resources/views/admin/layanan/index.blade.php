@extends('layouts.admin')
@section('title', 'Layanan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4>Layanan</h4><a href="{{ route('admin.layanan.create') }}" class="btn btn-primary">Tambah Layanan</a></div>
<div class="card"><div class="table-responsive"><table class="table"><thead><tr><th>Nama</th><th>Durasi</th><th>Interval</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($layanans as $layanan)<tr><td>{{ $layanan->nama }}</td><td>{{ $layanan->durasi_menit }} menit</td><td>{{ $layanan->interval_servis_hari ? $layanan->interval_servis_hari.' hari' : '-' }} / {{ $layanan->interval_servis_km ? number_format($layanan->interval_servis_km).' km' : '-' }}</td><td>{{ $layanan->aktif ? 'Aktif' : 'Nonaktif' }}</td><td><a href="{{ route('admin.layanan.edit', $layanan) }}" class="btn btn-sm btn-outline-primary">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.layanan.destroy', $layanan) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus atau nonaktifkan layanan ini?')">Hapus</button></form></td></tr>@empty<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>@endforelse
</tbody></table></div><div class="card-footer">{{ $layanans->links() }}</div></div>
@endsection
