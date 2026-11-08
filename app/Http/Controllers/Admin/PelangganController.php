<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PelangganRequest;
use App\Http\Requests\Admin\PelangganSearchRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(PelangganSearchRequest $request): View|JsonResponse
    {
        $search = trim((string) $request->validated('q'));

        $query = User::where('role', 'pelanggan')->withCount(['kendaraan', 'reservasi']);
        if ($search !== '') {
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('no_hp', 'like', "%{$search}%"));
        }

        $pelanggans = $query->latest()->paginate(5)->withQueryString();
        $subtitle = $search !== ''
            ? $pelanggans->total().' hasil untuk "'.$search.'".'
            : $pelanggans->total().' pelanggan terdaftar.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'results' => view('admin.pelanggan._results', [
                    'pelanggans' => $pelanggans,
                    'search' => $search,
                ])->render(),
                'modals' => view('admin.pelanggan._edit-modals', [
                    'pelanggans' => $pelanggans,
                ])->render(),
                'subtitle' => $subtitle,
                'total' => $pelanggans->total(),
            ]);
        }

        return view('admin.pelanggan.index', [
            'pelanggans' => $pelanggans,
            'pelangganBaru' => new User(['role' => UserRole::Pelanggan, 'aktif' => true]),
            'search' => $search,
            'subtitle' => $subtitle,
        ]);
    }

    public function create(): View
    {
        return view('admin.pelanggan.create', [
            'pelanggan' => new User(['role' => UserRole::Pelanggan, 'aktif' => true]),
        ]);
    }

    public function store(PelangganRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = UserRole::Pelanggan;
        $data['aktif'] = $request->boolean('aktif');

        User::create($data);

        return to_route('admin.pelanggan.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(User $pelanggan): View
    {
        abort_unless($pelanggan->role?->value === 'pelanggan', 404);

        $pelanggan->loadCount(['kendaraan', 'reservasi']);

        return view('admin.pelanggan.edit', ['pelanggan' => $pelanggan]);
    }

    public function update(PelangganRequest $request, User $pelanggan): RedirectResponse
    {
        abort_unless($pelanggan->role?->value === 'pelanggan', 404);

        $data = $request->validated();
        $data['aktif'] = $request->boolean('aktif');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $pelanggan->update($data);

        return to_route('admin.pelanggan.index')->with('status', 'Data pelanggan berhasil diperbarui.');
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

        $newPassword = Str::random(16);
        $pelanggan->forceFill([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60),
        ])->save();

        return back()
            ->with('status', 'Password pelanggan berhasil direset.')
            ->with('new_password', $newPassword)
            ->with('new_password_name', $pelanggan->name);
    }
}
