@extends('layouts.admin')
@section('title', 'Slot Waktu')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4>Slot Waktu</h4><a href="{{ route('admin.slot-waktu.create') }}" class="btn btn-primary">Buat Massal</a></div>
<div class="card"><div class="table-responsive"><table class="table"><thead><tr><th>Tanggal</th><th>Jam</th><th>Kapasitas</th><th>Reservasi aktif</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($slots as $slot)<tr><td>{{ $slot->tanggal->format('d/m/Y') }}</td><td>{{ substr($slot->jam_mulai, 0, 5) }} - {{ substr($slot->jam_selesai, 0, 5) }}</td><td>{{ $slot->kapasitas }}</td><td>{{ $slot->reservasi_count }}</td><td>{{ $slot->aktif ? 'Aktif' : 'Nonaktif' }}</td><td><a href="{{ route('admin.slot-waktu.edit', $slot) }}" class="btn btn-sm btn-outline-primary">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.slot-waktu.destroy', $slot) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="6" class="text-center">Belum ada data.</td></tr>@endforelse
</tbody></table></div><div class="card-footer">{{ $slots->links() }}</div></div>
@endsection
