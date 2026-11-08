<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSlotController extends Controller
{
    public function index(Request $request, SlotAvailabilityService $availability): View
    {
        $date = today();
        $error = null;
        $tanggal = $request->query('tanggal');

        if (is_string($tanggal) && $tanggal !== '') {
            $parsed = Carbon::hasFormat($tanggal, 'Y-m-d')
                ? Carbon::createFromFormat('!Y-m-d', $tanggal)
                : null;

            if ($parsed && $parsed->format('Y-m-d') === $tanggal) {
                $date = $parsed->startOfDay();
            } else {
                $error = 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD.';
            }
        }

        $slots = $availability->forDate($date);

        $view = $request->user()?->role === UserRole::Pelanggan
            ? 'customer.cek-slot'
            : 'public.cek-slot';

        return view($view, [
            'date' => $date,
            'slots' => $slots,
            'summary' => $availability->summary($slots),
            'error' => $error,
            'statusLabels' => SlotAvailabilityService::STATUS_LABELS,
            'statusBadges' => SlotAvailabilityService::STATUS_BADGES,
        ]);
    }
}
