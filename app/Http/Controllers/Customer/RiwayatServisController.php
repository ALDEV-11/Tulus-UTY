<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Servis;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatServisController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $kendaraans = $user->kendaraan()
            ->with('merk')
            ->orderBy('plat_nomor')
            ->get();

        $filters = $request->validate([
            'kendaraan' => ['nullable', 'integer'],
        ]);

        $kendaraanId = isset($filters['kendaraan']) ? (int) $filters['kendaraan'] : null;

        if ($kendaraanId && ! $kendaraans->contains('id', $kendaraanId)) {
            abort(404);
        }

        $riwayat = Servis::query()
            ->whereHas('kendaraan', fn ($query) => $query->where('user_id', $user->id))
            ->when($kendaraanId, fn ($query) => $query->where('kendaraan_id', $kendaraanId))
            ->with(['kendaraan.merk', 'reservasi.layanan'])
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('customer.riwayat.index', [
            'riwayat' => $riwayat,
            'kendaraans' => $kendaraans,
            'kendaraanId' => $kendaraanId,
        ]);
    }
}
