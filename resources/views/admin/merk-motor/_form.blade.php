@csrf
<div class="mb-3"><label class="form-label" for="nama">Nama merk</label><input id="nama" name="nama" value="{{ old('nama', $merk->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="form-check mb-3"><input type="hidden" name="aktif" value="0"><input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif" @checked(old('aktif', $merk->aktif))><label class="form-check-label" for="aktif">Aktif</label></div>
<button class="btn btn-primary">Simpan</button> <a class="btn btn-outline-secondary" href="{{ route('admin.merk-motor.index') }}">Batal</a>
