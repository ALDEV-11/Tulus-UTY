<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlotWaktuMassalRequest;
use App\Http\Requests\Admin\SlotWaktuUpdateRequest;
use App\Models\SlotWaktu;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SlotWaktuController extends Controller
{
    public function index(): View { return view('admin.slot-waktu.index', ['slots' => SlotWaktu::withCount('reservasi')->orderBy('tanggal')->orderBy('jam_mulai')->paginate(30)]); }
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

        return to_route('admin.slot-waktu.index')->with('status', "{$created} slot berhasil dibuat.");
    }
    public function edit(SlotWaktu $slot_waktu): View { return view('admin.slot-waktu.edit', ['slot' => $slot_waktu]); }
    public function update(SlotWaktuUpdateRequest $request, SlotWaktu $slot_waktu): RedirectResponse
    {
        $slot_waktu->update($request->validated());
        return to_route('admin.slot-waktu.index')->with('status', 'Slot berhasil diperbarui.');
    }
    public function destroy(SlotWaktu $slot_waktu): RedirectResponse
    {
        if ($slot_waktu->reservasi()->whereNotIn('status', ['dibatalkan', 'selesai'])->exists()) {
            return back()->withErrors(['slot' => 'Slot memiliki reservasi aktif dan tidak dapat dihapus.']);
        }
        $slot_waktu->delete();
        return to_route('admin.slot-waktu.index')->with('status', 'Slot berhasil dihapus.');
    }
}
