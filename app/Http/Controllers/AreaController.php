<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(): View
    {
        $areas = Area::query()
            ->withCount([
                'customers',
                'routers',
                'users as active_users_count' => function ($query) {
                    $query->wherePivot('active', true);
                },
            ])
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();

        return view('areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('areas.create', [
            'area' => new Area(['active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Area::create($this->validatedData($request));

        return redirect()
            ->route('areas.index')
            ->with('success', 'Wilayah operasional berhasil ditambahkan.');
    }

    public function edit(Area $area): View
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area): RedirectResponse
    {
        $area->update($this->validatedData($request, $area));

        return redirect()
            ->route('areas.index')
            ->with('success', 'Wilayah operasional berhasil diperbarui.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        if ($area->customers()->exists() || $area->routers()->exists()) {
            return back()->with(
                'error',
                'Wilayah tidak dapat dihapus karena masih memiliki pelanggan atau router. Nonaktifkan wilayah bila sudah tidak digunakan.'
            );
        }

        $area->delete();

        return redirect()
            ->route('areas.index')
            ->with('success', 'Wilayah operasional berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Area $area = null): array
    {
        $areaId = $area?->id;

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/',
                Rule::unique('areas', 'code')->ignore($areaId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Kode wilayah wajib diisi.',
            'code.max' => 'Kode wilayah maksimal 50 karakter.',
            'code.regex' => 'Kode wilayah hanya boleh memakai huruf, angka, titik, garis bawah, atau tanda hubung.',
            'code.unique' => 'Kode wilayah sudah digunakan.',
            'name.required' => 'Nama wilayah wajib diisi.',
            'name.max' => 'Nama wilayah maksimal 150 karakter.',
            'description.max' => 'Keterangan maksimal 2.000 karakter.',
        ]);

        return [
            'code' => strtoupper(trim($data['code'])),
            'name' => trim($data['name']),
            'description' => filled($data['description'] ?? null)
                ? trim($data['description'])
                : null,
            'active' => $request->boolean('active'),
        ];
    }
}
