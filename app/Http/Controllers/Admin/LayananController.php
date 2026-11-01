<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LayananRequest;
use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(): View
    {
        return view('admin.layanan.index', [
            'layanans' => Layanan::withCount('reservasi')->latest()->paginate(5),
            'layananBaru' => new Layanan(['aktif' => true]),
        ]);
    }
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
        $layanan->delete();

        return to_route('admin.layanan.index')->with('status', 'Layanan berhasil dihapus.');
    }
}
