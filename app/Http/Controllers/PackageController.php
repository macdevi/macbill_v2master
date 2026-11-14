<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        return view('packages.index', [
            'packages' => InternetPackage::with('routers')->latest()->paginate(20),
            'syncStates' => \Illuminate\Support\Facades\DB::table('package_sync_states')
                ->get()->keyBy(fn ($state) =>
                    $state->internet_package_id.':'.$state->router_id
                ),
            'routers' => Router::where('active', true)->orderBy('name')->get(),
        ]);
    }

    private function packageRouterFormData(?InternetPackage $package = null): array
    {
        $selectedRouterIds = $package ? $package->routers()->pluck('routers.id')
            ->map(fn ($id) => (int) $id)->all() : [];
        $routerUsage = $package ? \Illuminate\Support\Facades\DB::table('customers')
            ->where('internet_package_id', $package->id)
            ->select('router_id')->selectRaw('COUNT(*) AS total')
            ->groupBy('router_id')->pluck('total', 'router_id') : collect();
        $visibleIds = array_unique(array_merge($selectedRouterIds, $routerUsage->keys()->all()));
        $routers = Router::query()->where(function ($query) use ($visibleIds) {
            $query->where('active', true);
            if ($visibleIds) {
                $query->orWhereIn('id', $visibleIds);
            }
        })->orderBy('name')->get();
        return compact('package', 'selectedRouterIds', 'routerUsage', 'routers');
    }

    public function create()
    {
        return view('packages.create', $this->packageRouterFormData());
    }
    public function store(Request $request, \App\Services\PackageCatalogService $catalog)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        try {
            $catalog->save($request->all());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withInput()->withErrors([
                'package' => 'Paket tidak tersimpan. Periksa log aplikasi sebelum mencoba ulang.',
            ]);
        }
        return redirect()->route('packages.index')->with('success',
            'Paket dan pilihan router disimpan. Tidak ada sync ke MikroTik.'
        );
    }
    public function edit(InternetPackage $package)
    {
        return view('packages.edit', $this->packageRouterFormData($package));
    }
    public function update(Request $request, InternetPackage $package, \App\Services\PackageCatalogService $catalog)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        try {
            $catalog->save($request->all(), $package);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withInput()->withErrors([
                'package' => 'Perubahan tidak tersimpan. Periksa log aplikasi sebelum mencoba ulang.',
            ]);
        }
        return redirect()->route('packages.index')->with('success',
            'Paket dan pilihan router diperbarui. Tidak ada sync ke MikroTik.'
        );
    }
    public function destroy(InternetPackage $package)
    {
        $package->delete();

        return back()->with('success', 'Paket dihapus.');
    }

    public function import(Router $router, MikroTikService $mikrotik)
    {
        abort_unless($router->active, 422, 'Router sumber tidak aktif.');

        $profiles = collect($mikrotik->profiles($router))->map(function ($profile) {
            $rate = $profile['rate-limit'] ?? '';
            $download = 0;
            $upload = 0;
            $burst = '';
            $burstThreshold = '';
            $burstTime = '';

            if ($rate) {
                $segments = preg_split('/\s+/', trim($rate));
                $normal = $segments[0] ?? '0/0';
                $speed = explode('/', $normal);

                $download = (int) preg_replace('/[^0-9]/', '', $speed[0] ?? 0);
                $upload = (int) preg_replace('/[^0-9]/', '', $speed[1] ?? 0);
                $burst = $segments[1] ?? '';
                $burstThreshold = $segments[2] ?? '';
                $burstTime = $segments[3] ?? '';
            }

            return [
                'name' => $profile['name'] ?? '',
                'rate' => $rate,
                'download' => $download,
                'upload' => $upload,
                'burst' => $burst,
                'burst_threshold' => $burstThreshold,
                'burst_time' => $burstTime,
            ];
        });

        return view('packages.import', [
            'router' => $router,
            'profiles' => $profiles,
            'routers' => Router::where('active', true)->orderBy('name')->get(),
            'availablePackages' => InternetPackage::with('routers')
                ->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function importStore(
        Request $request,
        \App\Services\PackageImportService $importer
    ) {
        $data = $request->validate([
            'router_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('routers', 'id')
                    ->where('active', true),
            ],
            'packages' => ['required', 'array', 'min:1'],
        ]);

        $router = Router::findOrFail($data['router_id']);
        $result = $importer->importPackages($router, $data['packages']);

        return redirect()->route('packages.index')->with(
            'success',
            'Import dari '.$router->name.': '
            .$result['created'].' paket baru, '
            .$result['linked'].' paket dihubungkan, '
            .$result['unchanged'].' paket lama dipertahankan. '
            .'Tidak ada perubahan yang dikirim ke MikroTik.'
        );
    }

    public function sync(
        Request $request,
        InternetPackage $package,
        MikroTikService $mikrotik
    ) {
        $data = $request->validate([
            'router_id' => ['required', 'integer'],
        ]);

        $router = $package->routers()
            ->where('routers.id', $data['router_id'])
            ->where('routers.active', true)
            ->first();

        if (!$router || !$package->active || blank($package->mikrotik_profile)) {
            return back()->withErrors([
                'sync' => 'Router tidak terhubung ke paket, tidak aktif, '
                    .'atau profil paket belum valid.',
            ]);
        }

        $fingerprint = self::syncFingerprint($package);

        try {
            $this->recordSyncState($package, $router, 'pending');
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors([
                'sync' => 'Pencatatan status gagal. Tidak ada sync yang dikirim.',
            ]);
        }

        try {
            $response = $mikrotik->syncProfile($router, $package);
            MikroTikService::assertSuccessfulResponse($response);
        } catch (\Throwable $exception) {
            report($exception);

            try {
                $this->recordSyncState($package, $router, 'failed');
            } catch (\Throwable $loggingException) {
                report($loggingException);
            }

            return back()->withErrors([
                'sync' => 'Sync ke '.$router->name
                    .' gagal. Periksa log sebelum mencoba ulang.',
            ]);
        }

        try {
            $this->recordSyncState($package, $router, 'success', $fingerprint);
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors([
                'sync' => 'Router merespons tanpa error, tetapi status gagal disimpan. '
                    .'Periksa router sebelum mengulangi sync.',
            ]);
        }

        return back()->with(
            'success',
            'Paket '.$package->name.' berhasil disinkronkan ke '.$router->name.'.'
        );
    }

    public static function syncFingerprint(InternetPackage $package): string
    {
        $fields = [
            'mikrotik_profile', 'download_speed', 'upload_speed',
            'burst_limit', 'burst_threshold', 'burst_time', 'priority',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = (string) ($package->getAttribute($field) ?? '');
        }

        return hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function recordSyncState(
        InternetPackage $package,
        Router $router,
        string $status,
        ?string $fingerprint = null
    ): void {
        $values = [
            'status' => $status,
            'attempted_at' => now(),
            'updated_at' => now(),
        ];

        if ($status === 'success') {
            $values['synced_at'] = now();
            $values['fingerprint'] = $fingerprint;
        }

        \Illuminate\Support\Facades\DB::table('package_sync_states')
            ->updateOrInsert(
                [
                    'internet_package_id' => $package->id,
                    'router_id' => $router->id,
                ],
                $values
            );
    }


}
