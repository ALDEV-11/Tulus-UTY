<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusReservasi;
use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\SlotWaktu;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\RevenueChartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, RevenueChartService $revenueChart): View
    {
        $reservasiPerStatus = Reservasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status->value => (int) $row->total]);

        $servisPerStatus = Servis::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status->value => (int) $row->total]);

        $statusReservasi = [
            StatusReservasi::MenungguKonfirmasi->value => 'Menunggu Konfirmasi',
            StatusReservasi::Dikonfirmasi->value => 'Dikonfirmasi',
            StatusReservasi::CheckIn->value => 'Check In',
            StatusReservasi::Selesai->value => 'Selesai',
            StatusReservasi::Dibatalkan->value => 'Dibatalkan',
        ];

        $chartStatusReservasi = ['labels' => [], 'data' => []];
        foreach ($statusReservasi as $status => $label) {
            $chartStatusReservasi['labels'][] = $label;
            $chartStatusReservasi['data'][] = $reservasiPerStatus[$status] ?? 0;
        }

        $chartReservasiHarian = ['labels' => [], 'data' => []];
        $reservasiHarian = Reservasi::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartReservasiHarian['labels'][] = $date->translatedFormat('d M');
            $chartReservasiHarian['data'][] = (int) ($reservasiHarian[$date->toDateString()] ?? 0);
        }

        $tahunPendapatan = $request->integer('tahun');
        $chartTotalRevenue = $revenueChart->build(
            Servis::query(),
            $tahunPendapatan > 0 ? $tahunPendapatan : null
        );

        $layananPopuler = Layanan::query()
            ->withCount('reservasi')
            ->orderByDesc('reservasi_count')
            ->orderBy('nama')
            ->limit(5)
            ->get();

        $chartLayananPopuler = [
            'labels' => $layananPopuler->pluck('nama')->all(),
            'data' => $layananPopuler->pluck('reservasi_count')->map(fn ($total) => (int) $total)->all(),
        ];

        $slotQuery = SlotWaktu::query()
            ->withCount([
                'reservasi as reservasi_aktif_count' => fn ($query) => $query->aktif(),
            ]);

        $slotHariIni = (clone $slotQuery)
            ->whereDate('tanggal', today())
            ->orderBy('jam_mulai')
            ->get();

        $slotMendatang = (clone $slotQuery)
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        $slotTersediaHariIni = $slotHariIni
            ->filter(fn ($slot) => $slot->aktif && $slot->reservasi_aktif_count < $slot->kapasitas)
            ->count();

        $whatsappPerStatus = WhatsappLog::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $reservasiTerbaru = Reservasi::query()
            ->with(['pelanggan', 'kendaraan.merk', 'slot', 'layanan'])
            ->latest()
            ->limit(5)
            ->get();

        $servisTerbaru = Servis::query()
            ->with(['kendaraan.merk', 'mekanik'])
            ->latest('tanggal_masuk')
            ->limit(5)
            ->get();

        $totalPendapatan = (float) Servis::sum('total_biaya');

        return view('admin.dashboard', [
            'totalReservasi' => $reservasiPerStatus->sum(),
            'reservasiMenunggu' => $reservasiPerStatus[StatusReservasi::MenungguKonfirmasi->value] ?? 0,
            'totalPendapatan' => $totalPendapatan,
            'totalServis' => $servisPerStatus->sum(),
            'servisAktif' => ($servisPerStatus['dijadwalkan'] ?? 0) + ($servisPerStatus['dikerjakan'] ?? 0),
            'servisSelesai' => $servisPerStatus['selesai'] ?? 0,
            'pelangganTotal' => User::where('role', 'pelanggan')->count(),
            'pelangganAktif' => User::where('role', 'pelanggan')->where('aktif', true)->count(),
            'totalKendaraan' => Kendaraan::count(),
            'totalMerk' => MerkMotor::count(),
            'merkAktif' => MerkMotor::where('aktif', true)->count(),
            'totalLayanan' => Layanan::count(),
            'layananAktif' => Layanan::where('aktif', true)->count(),
            'slotHariIni' => $slotHariIni->count(),
            'slotTersediaHariIni' => $slotTersediaHariIni,
            'slotMendatang' => $slotMendatang,
            'slotMendatangTotal' => SlotWaktu::whereDate('tanggal', '>=', today())->where('aktif', true)->count(),
            'whatsappTerkirim' => (int) ($whatsappPerStatus['terkirim'] ?? 0),
            'whatsappMenunggu' => (int) ($whatsappPerStatus['menunggu'] ?? 0),
            'chartStatusReservasi' => $chartStatusReservasi,
            'chartReservasiHarian' => $chartReservasiHarian,
            'chartTotalRevenue' => $chartTotalRevenue,
            'chartLayananPopuler' => $chartLayananPopuler,
            'reservasiTerbaru' => $reservasiTerbaru,
            'servisTerbaru' => $servisTerbaru,
        ]);
    }
}
