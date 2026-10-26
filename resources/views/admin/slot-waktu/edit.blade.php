@php($pageTitle = 'Edit Slot Waktu')
@include('admin.partials.page-head')
<div class="card"><div class="card-header"><h5>Edit Slot {{ $slot->tanggal->format('d/m/Y') }}</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.slot-waktu.update', $slot) }}">@csrf @method('PUT')
<div class="mb-3"><label class="form-label">Kapasitas</label><input type="number" name="kapasitas" value="{{ old('kapasitas', $slot->kapasitas) }}" class="form-control" min="1" required></div><div class="form-check mb-3"><input type="hidden" name="aktif" value="0"><input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif" @checked(old('aktif', $slot->aktif))><label class="form-check-label" for="aktif">Aktif</label></div>
<button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="{{ route('admin.slot-waktu.index') }}">Batal</a></form></div></div>
@include('admin.partials.page-foot')
