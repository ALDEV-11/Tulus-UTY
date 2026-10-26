<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LayananRequest;
use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(): View { return view('admin.layanan.index', ['layanans' => Layanan::latest()->paginate(15)]); }
    public function create(): View { return view('admin.layanan.create', ['layanan' => new Layanan(['aktif' => true])]); }
    public function store(LayananRequest $request): RedirectResponse
    {
        Layanan::create($request->validated());
        return to_route('admin.layanan.index')->with('status', 'Layanan berhasil ditambahkan.');
    }
    public function edit(Layanan $layanan): View { return view('admin.layanan.edit', compact('layanan')); }
    public function update(LayananRequest $request, Layanan $layanan): RedirectResponse
    {
        $layanan->update($request->validated());
        return to_route('admin.layanan.index')->with('status', 'Layanan berhasil diperbarui.');
    }
    public function destroy(Layanan $layanan): RedirectResponse
    {
        if ($layanan->reservasi()->exists()) {
            $layanan->update(['aktif' => false]);
            return to_route('admin.layanan.index')->with('status', 'Layanan sudah dipakai, sehingga hanya dinonaktifkan.');
        }
        $layanan->delete();
        return to_route('admin.layanan.index')->with('status', 'Layanan berhasil dihapus.');
    }
}
