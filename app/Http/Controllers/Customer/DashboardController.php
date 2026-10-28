<?php

namespace App\Http\Controllers\Customer;

use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\SlotWaktu;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private SlotAvailabilityService $slotAvailability,
    ) {}

    public function index(Request $request): View
    {
        return view('customer.dashboard', $this->viewData($request));
    }

    public function show(Reservasi $reservasi): View
    {
        Gate::authorize('view', $reservasi);

        $reservasi->load(['kendaraan.merk', 'slot', 'layanan', 'servis.mekanik']);

        return view('customer.reservasi.show', ['reservasi' => $reservasi]);
    }

    private function viewData(Request $request): array
    {
        $user = $request->user();

        $reservasiQuery = Reservasi::query()->where('user_id', $user->id);
        $servisQuery = Servis::query()
            ->whereHas('kendaraan', fn ($query) => $query->where('user_id', $user->id));

        $ringkasan = [
            'reservasi_aktif' => (clone $reservasiQuery)->aktif()->count(),
            'servis_berlangsung' => (clone $servisQuery)
                ->whereIn('status', [StatusServis::Menunggu->value, StatusServis::Dikerjakan->value])
                ->count(),
            'servis_selesai' => (clone $servisQuery)
                ->whereIn('status', [StatusServis::Selesai->value, StatusServis::Diambil->value])
                ->count(),
            'kendaraan' => $user->kendaraan()->count(),
        ];

        $servisTerakhir = (clone $servisQuery)
            ->with(['kendaraan.merk', 'reservasi.layanan', 'mekanik'])
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $requestedSlotId = $request->integer('slot');
        $requestedSlot = null;
        $requestedSlotUnavailable = false;

        if ($requestedSlotId > 0) {
            $requestedSlot = SlotWaktu::query()->find($requestedSlotId);

            if ($requestedSlot) {
                $requestedSlot = $this->slotAvailability->decorate($requestedSlot);
                $requestedSlotUnavailable = ! $requestedSlot->aktif
                    || $requestedSlot->lewat
                    || $requestedSlot->sisa <= 0;
            } else {
                $requestedSlotUnavailable = true;
            }
        }

        $availableSlots = $this->slotAvailability->upcomingAvailable();

        return [
            'ringkasan' => $ringkasan,
            'servisTerakhir' => $servisTerakhir,
            'slotTerdekat' => $availableSlots->take(5),
            'kendaraans' => $user->kendaraan()->with('merk')->orderBy('plat_nomor')->get(),
            'layanans' => Layanan::query()->where('aktif', true)->orderBy('nama')->get(),
            'merks' => MerkMotor::query()->where('aktif', true)->orderBy('nama')->get(),
            'availableSlots' => $availableSlots,
            'requestedSlot' => $requestedSlot,
            'requestedSlotId' => $requestedSlotId > 0 ? $requestedSlotId : null,
            'requestedSlotUnavailable' => $requestedSlotUnavailable,
            'reservasiSaya' => (clone $reservasiQuery)
                ->with(['kendaraan.merk', 'slot', 'layanan', 'servis'])
                ->latest()
                ->paginate(10, ['*'], 'reservasi_page')
                ->withQueryString(),
        ];
    }
}
