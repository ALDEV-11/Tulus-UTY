<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisWhatsapp;
use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WhatsappLogController extends Controller
{
    private const STATUS_LABELS = [
        'menunggu' => 'Menunggu',
        'terkirim' => 'Terkirim',
        'gagal' => 'Gagal',
    ];

    private const STATUS_BADGES = [
        'menunggu' => 'warning',
        'terkirim' => 'success',
        'gagal' => 'danger',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(self::STATUS_LABELS))],
            'jenis' => ['nullable', Rule::enum(JenisWhatsapp::class)],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', ...($request->filled('dari') ? ['after_or_equal:dari'] : [])],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $status = $filters['status'] ?? null;
        $jenis = $filters['jenis'] ?? null;
        $dari = $filters['dari'] ?? null;
        $sampai = $filters['sampai'] ?? null;

        $logs = WhatsappLog::query()
            ->with(['user', 'reservasi'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nomor_tujuan', 'like', "%{$search}%")
                        ->orWhere('pesan', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('reservasi', fn ($query) => $query->where('kode', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($jenis, fn ($query) => $query->where('jenis', $jenis))
            ->when($dari, fn ($query) => $query->whereDate('created_at', '>=', $dari))
            ->when($sampai, fn ($query) => $query->whereDate('created_at', '<=', $sampai))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.whatsapp-log.index', [
            'logs' => $logs,
            'statusLabels' => self::STATUS_LABELS,
            'statusBadges' => self::STATUS_BADGES,
            'search' => $search,
            'status' => $status,
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
        ]);
    }
}
