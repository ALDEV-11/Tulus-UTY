<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlotWaktuMassalRequest;
use App\Http\Requests\Admin\SlotWaktuUpdateRequest;
use App\Models\SlotWaktu;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SlotWaktuController extends Controller
{
    public function index(Request $request): View
    {
        $month = now()->startOfMonth();
        $bulan = $request->query('bulan');

        if (is_string($bulan) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
            $month = Carbon::createFromFormat('!Y-m', $bulan)->startOfMonth();
        }

        $selectedDate = $this->resolveTanggal($request->query('tanggal'));

        if ($selectedDate) {
            $month = $selectedDate->copy()->startOfMonth();
        }

        $bulan = $month->format('Y-m');
        $calendarStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarSlots = SlotWaktu::query()
            ->withCount([
                'reservasi as reservasi_aktif_count' => fn ($query) => $query
                    ->aktif(),
            ])
            ->whereBetween('tanggal', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy(fn (SlotWaktu $slot) => $slot->tanggal->toDateString());

        $monthSlots = $calendarSlots
            ->flatten()
            ->filter(fn (SlotWaktu $slot) => $slot->tanggal->format('Y-m') === $bulan)
            ->values();

        $daySlots = $selectedDate
            ? $calendarSlots->get($selectedDate->toDateString(), collect())
            : collect();

        return view('admin.slot-waktu.index', [
            'bulan' => $bulan,
            'month' => $month,
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
            'calendarSlots' => $calendarSlots,
            'selectedDate' => $selectedDate,
            'daySlots' => $daySlots,
            'monthSummary' => $this->summarizeSlots($monthSlots),
            'daySummary' => $this->summarizeSlots($daySlots),
        ]);
    }
    public function create(): View { return view('admin.slot-waktu.create'); }
    public function store(SlotWaktuMassalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $startDate = Carbon::parse($data['tanggal_mulai']);
        $endDate = Carbon::parse($data['tanggal_selesai']);
        $startMinutes = Carbon::createFromFormat('H:i', $data['jam_mulai'])->hour * 60 + Carbon::createFromFormat('H:i', $data['jam_mulai'])->minute;
        $endMinutes = Carbon::createFromFormat('H:i', $data['jam_selesai'])->hour * 60 + Carbon::createFromFormat('H:i', $data['jam_selesai'])->minute;
        $created = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            for ($minute = $startMinutes; $minute + $data['durasi_slot'] <= $endMinutes; $minute += $data['durasi_slot']) {
                $slot = SlotWaktu::firstOrCreate([
                    'tanggal' => $date->toDateString(),
                    'jam_mulai' => sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60),
                ], [
                    'jam_selesai' => sprintf('%02d:%02d', intdiv($minute + $data['durasi_slot'], 60), ($minute + $data['durasi_slot']) % 60),
                    'kapasitas' => $data['kapasitas'],
                    'aktif' => true,
                ]);
                if ($slot->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        return $this->redirectToCalendar($request, "{$created} slot berhasil dibuat.", $data['tanggal_mulai']);
    }
    public function edit(SlotWaktu $slot_waktu): View
    {
        $slot_waktu->loadCount([
            'reservasi as reservasi_aktif_count' => fn ($query) => $query
                ->aktif(),
        ]);

        return view('admin.slot-waktu.edit', ['slot' => $slot_waktu]);
    }
    public function update(SlotWaktuUpdateRequest $request, SlotWaktu $slot_waktu): RedirectResponse
    {
        $slot_waktu->update($request->validated());
        return $this->redirectToCalendar($request, 'Slot berhasil diperbarui.');
    }
    public function destroy(Request $request, SlotWaktu $slot_waktu): RedirectResponse
    {
        if ($slot_waktu->reservasi()->aktif()->exists()) {
            return back()->withErrors(['slot' => 'Slot memiliki reservasi aktif dan tidak dapat dihapus.']);
        }
        $slot_waktu->delete();
        return $this->redirectToCalendar($request, 'Slot berhasil dihapus.');
    }

    private function resolveTanggal(mixed $tanggal): ?Carbon
    {
        if (! is_string($tanggal) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $tanggal)->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        return $date->format('Y-m-d') === $tanggal ? $date : null;
    }

    private function summarizeSlots(Collection $slots): array
    {
        $summary = ['total' => 0, 'tersedia' => 0, 'sebagian' => 0, 'penuh' => 0, 'nonaktif' => 0];

        foreach ($slots as $slot) {
            $summary['total']++;

            if (! $slot->aktif) {
                $summary['nonaktif']++;
            } elseif ($slot->reservasi_aktif_count >= $slot->kapasitas) {
                $summary['penuh']++;
            } elseif ($slot->reservasi_aktif_count > 0) {
                $summary['sebagian']++;
            } else {
                $summary['tersedia']++;
            }
        }

        return $summary;
    }

    private function redirectToCalendar(Request $request, string $status, ?string $preferredTanggal = null): RedirectResponse
    {
        $tanggal = $preferredTanggal ?? $request->input('_tanggal');
        $bulan = $request->input('_bulan');
        $parsedTanggal = is_string($tanggal) ? $this->resolveTanggal($tanggal) : null;

        if ($parsedTanggal) {
            $bulan = $parsedTanggal->format('Y-m');
            $tanggal = $parsedTanggal->toDateString();
        } else {
            $tanggal = null;
        }

        if (! is_string($bulan) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
            $bulan = null;
        }

        return to_route('admin.slot-waktu.index', array_filter([
            'bulan' => $bulan,
            'tanggal' => $tanggal,
        ], fn ($value) => $value !== null && $value !== ''))->with('status', $status);
    }
}
