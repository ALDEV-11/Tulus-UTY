<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PelangganSearchRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(PelangganSearchRequest $request): View
    {
        $query = User::where('role', 'pelanggan');
        if ($request->filled('q')) {
            $term = $request->validated('q');
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('no_hp', 'like', "%{$term}%"));
        }
        return view('admin.pelanggan.index', ['pelanggans' => $query->latest()->paginate(15)->withQueryString()]);
    }
    public function toggle(User $pelanggan): RedirectResponse
    {
        abort_unless($pelanggan->role?->value === 'pelanggan', 404);
        $pelanggan->update(['aktif' => ! $pelanggan->aktif]);
        return back()->with('status', 'Status pelanggan berhasil diperbarui.');
    }
    public function resetPassword(User $pelanggan): RedirectResponse
    {
        abort_unless($pelanggan->role?->value === 'pelanggan', 404);
        $pelanggan->forceFill(['password' => Hash::make(Str::random(16)), 'remember_token' => Str::random(60)])->save();
        return back()->with('status', 'Password direset dan token Remember Me diganti.');
    }
}
