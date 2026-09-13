<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with([
            'areas' => fn ($query) => $query
                ->wherePivot('active', true)
                ->orderBy('areas.name'),
        ])->orderByDesc('id')->get();

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'areas' => fn ($query) => $query
                ->wherePivot('active', true)
                ->orderBy('areas.name'),
        ]);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        $areas = Area::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return view('users.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'area_ids' => ['required', 'array', 'min:1'],
            'area_ids.*' => [
                'integer',
                Rule::exists('areas', 'id')->where(fn ($query) => $query->where('active', true)),
            ],
        ], [
            'area_ids.required' => 'Pilih minimal satu wilayah penugasan.',
            'area_ids.min' => 'Pilih minimal satu wilayah penugasan.',
            'area_ids.*.exists' => 'Salah satu wilayah yang dipilih tidak tersedia atau sudah nonaktif.',
        ]);

        $user = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $this->syncAreas($user, $data['area_ids'], $request->user()?->id);

        return redirect('/users')
            ->with('success', 'User dan penugasan wilayah berhasil dibuat.');
    }

    public function edit(User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun Super Admin tidak dapat diubah dari halaman ini.');

        $areas = Area::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $user->load([
            'areas' => fn ($query) => $query->wherePivot('active', true),
        ]);

        return view('users.edit', compact('user', 'areas'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun Super Admin tidak dapat diubah dari halaman ini.');

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'area_ids' => ['required', 'array', 'min:1'],
            'area_ids.*' => [
                'integer',
                Rule::exists('areas', 'id')->where(fn ($query) => $query->where('active', true)),
            ],
        ], [
            'area_ids.required' => 'Pilih minimal satu wilayah penugasan.',
            'area_ids.min' => 'Pilih minimal satu wilayah penugasan.',
            'area_ids.*.exists' => 'Salah satu wilayah yang dipilih tidak tersedia atau sudah nonaktif.',
        ]);

        $user->update([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:6'],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $this->syncAreas($user, $data['area_ids'], $request->user()?->id);

        return redirect('/users')
            ->with('success', 'User dan penugasan wilayah berhasil diperbarui.');
    }

    public function updatePassword(Request $request, User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'Password Super Admin tidak dapat diubah dari halaman ini.');

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()
            ->with('success', 'Password berhasil diganti.');
    }

    public function destroy(User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun Super Admin tidak dapat dihapus dari halaman ini.');

        $user->delete();

        return redirect('/users')
            ->with('success', 'User berhasil dihapus.');
    }

    private function syncAreas(User $user, array $areaIds, ?int $assignedBy): void
    {
        $existingAssignments = $user->areas()
            ->get()
            ->keyBy('id');

        $assignments = collect($areaIds)
            ->unique()
            ->mapWithKeys(function ($areaId) use ($existingAssignments, $assignedBy) {
                $existing = $existingAssignments->get((int) $areaId);

                return [
                    (int) $areaId => [
                        'active' => true,
                        'assigned_by' => $existing?->pivot?->assigned_by ?? $assignedBy,
                        'assigned_at' => $existing?->pivot?->assigned_at ?? now(),
                    ],
                ];
            })
            ->all();

        $user->areas()->sync($assignments);
    }
}
