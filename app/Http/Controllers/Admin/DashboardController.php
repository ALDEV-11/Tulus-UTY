<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\User;
use App\Services\RevenueChartService;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(RevenueChartService $revenueChart): View
    {
        $reservasiPerStatus = Reservasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $servisPerStatus = Servis::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $chartStatusReservasi = ['labels' => [], 'data' => []];
        foreach ([
            StatusReservasi::MenungguKonfirmasi->value => 'Menunggu Konfirmasi',
            StatusReservasi::Dikonfirmasi->value => 'Dikonfirmasi',
            StatusReservasi::CheckIn->value => 'Check In',
            StatusReservasi::Selesai->value => 'Selesai',
            StatusReservasi::Dibatalkan->value => 'Dibatalkan',
        ] as $status => $label) {
            $chartStatusReservasi['labels'][] = $label;
            $chartStatusReservasi['data'][] = (int) ($reservasiPerStatus[$status] ?? 0);
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

        $chartTotalRevenue = $revenueChart->build(Servis::query());

        $reservasiTerbaru = Reservasi::query()
            ->with(['pelanggan', 'kendaraan.merk', 'slot', 'layanan'])
            ->latest()
            ->limit(5)
            ->get();



        return view('admin.dashboard', [
            'totalReservasi' => $reservasiPerStatus->sum(),
            'reservasiMenunggu' => (int) ($reservasiPerStatus[StatusReservasi::MenungguKonfirmasi->value] ?? 0),
            'totalPendapatan' => (float) Servis::sum('total_biaya'),
            'totalServis' => $servisPerStatus->sum(),
            'servisAktif' => (int) ($servisPerStatus[StatusServis::Menunggu->value] ?? 0)
                + (int) ($servisPerStatus[StatusServis::Dikerjakan->value] ?? 0),
            'servisSelesai' => (int) ($servisPerStatus[StatusServis::Selesai->value] ?? 0)
                + (int) ($servisPerStatus[StatusServis::Diambil->value] ?? 0),
            'pelangganTotal' => User::where('role', 'pelanggan')->count(),
            'pelangganAktif' => User::where('role', 'pelanggan')->where('aktif', true)->count(),
            'totalKendaraan' => Kendaraan::count(),
            'chartStatusReservasi' => $chartStatusReservasi,
            'chartReservasiHarian' => $chartReservasiHarian,
            'chartTotalRevenue' => $chartTotalRevenue,
            'reservasiTerbaru' => $reservasiTerbaru,

        ]);
    }
}
