<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\KendaraanRequest;
use App\Models\Kendaraan;
use App\Models\MerkMotor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(Request $request): View
    {
        return view('customer.kendaraan.index', [
            'kendaraans' => $request->user()
                ->kendaraan()
                ->with('merk')
                ->withCount(['reservasi', 'servis'])
                ->orderBy('plat_nomor')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('customer.kendaraan.create', ['merks' => $this->merks()]);
    }

    public function store(KendaraanRequest $request): RedirectResponse
    {
        $kendaraan = $request->user()->kendaraan()->create($request->validated());

        return to_route('kendaraan.index')
            ->with('status', "Kendaraan {$kendaraan->plat_nomor} berhasil ditambahkan.");
    }

    public function edit(Kendaraan $kendaraan): View
    {
        Gate::authorize('update', $kendaraan);

        return view('customer.kendaraan.edit', [
            'kendaraan' => $kendaraan,
            'merks' => $this->merks(),
        ]);
    }

    public function update(KendaraanRequest $request, Kendaraan $kendaraan): RedirectResponse
    {
        Gate::authorize('update', $kendaraan);

        $kendaraan->update($request->validated());

        return to_route('kendaraan.index')
            ->with('status', "Kendaraan {$kendaraan->plat_nomor} berhasil diperbarui.");
    }

    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        Gate::authorize('delete', $kendaraan);

        if ($kendaraan->reservasi()->exists() || $kendaraan->servis()->exists()) {
            return back()->withErrors([
                'kendaraan' => 'Kendaraan memiliki riwayat reservasi atau servis sehingga tidak dapat dihapus.',
            ]);
        }

        $platNomor = $kendaraan->plat_nomor;
        $kendaraan->delete();

        return to_route('kendaraan.index')
            ->with('status', "Kendaraan {$platNomor} berhasil dihapus.");
    }

    private function merks(): Collection
    {
        return MerkMotor::query()
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();
    }
}
