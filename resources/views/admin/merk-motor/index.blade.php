@extends('layouts.admin')
@section('title', 'Merk Motor')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Merk Motor</h4>
    <a href="{{ route('admin.merk-motor.create') }}" class="btn btn-primary">Tambah Merk</a>
</div>
<div class="card"><div class="table-responsive text-nowrap"><table class="table">
    <thead><tr><th>Nama</th><th>Status</th><th>Kendaraan</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse ($merks as $merk)
        <tr><td>{{ $merk->nama }}</td><td><span class="badge bg-label-{{ $merk->aktif ? 'success' : 'secondary' }}">{{ $merk->aktif ? 'Aktif' : 'Nonaktif' }}</span></td><td>{{ $merk->kendaraan_count ?? $merk->kendaraan()->count() }}</td><td class="text-end"><a href="{{ route('admin.merk-motor.edit', $merk) }}" class="btn btn-sm btn-outline-primary">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.merk-motor.destroy', $merk) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus atau nonaktifkan merk ini?')">Hapus</button></form></td></tr>
    @empty <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>@endforelse
    </tbody>
</table></div><div class="card-footer">{{ $merks->links() }}</div></div>
@endsection
