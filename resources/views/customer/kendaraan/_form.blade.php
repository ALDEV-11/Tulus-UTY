@php($kendaraan = $kendaraan ?? null)

<div class="row g-4">
  <div class="col-12 col-md-6">
    <label class="form-label" for="merk_motor_id">Merk</label>
    <select
      class="form-select @error('merk_motor_id') is-invalid @enderror"
      id="merk_motor_id"
      name="merk_motor_id"
      required>
      <option value="">Pilih merk</option>
      @foreach ($merks as $merk)
        <option value="{{ $merk->id }}" @selected((string) old('merk_motor_id', $kendaraan?->merk_motor_id) === (string) $merk->id)>
          {{ $merk->nama }}
        </option>
      @endforeach
    </select>
    @error('merk_motor_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label" for="plat_nomor">Nomor Polisi</label>
    <input
      type="text"
      class="form-control @error('plat_nomor') is-invalid @enderror"
      id="plat_nomor"
      name="plat_nomor"
      value="{{ old('plat_nomor', $kendaraan?->plat_nomor) }}"
      maxlength="15"
      placeholder="Contoh: AB 1234 CD"
      autocomplete="off"
      required>
    @error('plat_nomor')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">Huruf otomatis menjadi kapital dan spasi dirapikan.</div>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label" for="model">Tipe</label>
    <input
      type="text"
      class="form-control @error('model') is-invalid @enderror"
      id="model"
      name="model"
      value="{{ old('model', $kendaraan?->model) }}"
      maxlength="100"
      placeholder="Contoh: Beat Street"
      required>
    @error('model')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label" for="tahun">Tahun</label>
    <input
      type="number"
      class="form-control @error('tahun') is-invalid @enderror"
      id="tahun"
      name="tahun"
      value="{{ old('tahun', $kendaraan?->tahun) }}"
      min="1980"
      max="{{ now()->year + 1 }}"
      placeholder="Contoh: 2022"
      required>
    @error('tahun')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>
