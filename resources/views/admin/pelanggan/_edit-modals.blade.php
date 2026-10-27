@foreach ($pelanggans as $pelanggan)
    <div class="modal fade" id="modalEditPelanggan{{ $pelanggan->id }}" tabindex="-1" aria-labelledby="modalEditPelanggan{{ $pelanggan->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.pelanggan.update', $pelanggan) }}">
                    @method('PUT')
                    <input type="hidden" name="_modal" value="modalEditPelanggan{{ $pelanggan->id }}">
                    <input type="hidden" name="_record" value="{{ $pelanggan->id }}">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1" id="modalEditPelanggan{{ $pelanggan->id }}Label">Edit Pelanggan</h5>
                            <small class="text-body-secondary">{{ $pelanggan->name }}</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        @include('admin.pelanggan._form', [
                            'pelanggan' => $pelanggan,
                            'prefix' => 'edit-pelanggan-'.$pelanggan->id.'-',
                            'useOld' => old('_modal') === 'modalEditPelanggan'.$pelanggan->id,
                            'inModal' => true,
                        ])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
