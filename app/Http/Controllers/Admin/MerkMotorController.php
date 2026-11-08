<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MerkMotorRequest;
use App\Models\MerkMotor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MerkMotorController extends Controller
{
    public function index(): View
    {
        return view('admin.merk-motor.index', [
            'merks' => MerkMotor::withCount('kendaraan')->latest()->paginate(5),
            'merkBaru' => new MerkMotor(['aktif' => true]),
        ]);
    }

    public function create(): View
    {
        return view('admin.merk-motor.create', ['merk' => new MerkMotor(['aktif' => true])]);
    }

    public function store(MerkMotorRequest $request): RedirectResponse
    {
        MerkMotor::create($request->validated());

        return to_route('admin.merk-motor.index')->with('status', 'Merk motor berhasil ditambahkan.');
    }

    public function edit(MerkMotor $merk_motor): View
    {
        return view('admin.merk-motor.edit', ['merk' => $merk_motor]);
    }

    public function update(MerkMotorRequest $request, MerkMotor $merk_motor): RedirectResponse
    {
        $merk_motor->update($request->validated());

        return to_route('admin.merk-motor.index')->with('status', 'Merk motor berhasil diperbarui.');
    }

    public function destroy(MerkMotor $merk_motor): RedirectResponse
    {
        if ($merk_motor->kendaraan()->exists()) {
            $merk_motor->update(['aktif' => false]);

            return to_route('admin.merk-motor.index')->with('status', 'Merk sudah dipakai, sehingga hanya dinonaktifkan.');
        }

        $merk_motor->delete();

        return to_route('admin.merk-motor.index')->with('status', 'Merk motor berhasil dihapus.');
    }
}
