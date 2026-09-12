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
            'packages' => InternetPackage::latest()->paginate(20),
            'routers' => Router::where('active', true)->get(),
        ]);
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'mikrotik_profile' => 'required|string|max:100|unique:internet_packages,mikrotik_profile',
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'monthly_price' => 'nullable|numeric|min:0',
            'burst_limit' => 'nullable|string|max:50',
            'burst_threshold' => 'nullable|string|max:50',
            'burst_time' => 'nullable|string|max:50',
            'priority' => 'nullable|integer|min:1|max:8',
        ]);

        $data['monthly_price'] = $data['monthly_price'] ?? 0;
        $data['priority'] = $data['priority'] ?? 8;

        InternetPackage::create($data);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket dibuat.');
    }

    public function edit(InternetPackage $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, InternetPackage $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'mikrotik_profile' => 'required|string|max:100|unique:internet_packages,mikrotik_profile,' . $package->id,
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'monthly_price' => 'nullable|numeric|min:0',
            'burst_limit' => 'nullable|string|max:50',
            'burst_threshold' => 'nullable|string|max:50',
            'burst_time' => 'nullable|string|max:50',
            'priority' => 'nullable|integer|min:1|max:8',
        ]);

        $data['monthly_price'] = $data['monthly_price'] ?? 0;
        $data['priority'] = $data['priority'] ?? 8;

        $package->update($data);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Paket diperbarui.');
    }

    public function destroy(InternetPackage $package)
    {
        $package->delete();

        return back()->with('success', 'Paket dihapus.');
    }

    public function import(Router $router, MikroTikService $mikrotik)
    {
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
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'router_id' => 'required|exists:routers,id',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:100',
            'packages.*.mikrotik_profile' => 'required|string|max:100',
            'packages.*.download_speed' => 'nullable|integer|min:0',
            'packages.*.upload_speed' => 'nullable|integer|min:0',
            'packages.*.monthly_price' => 'nullable|numeric|min:0',
            'packages.*.burst_limit' => 'nullable|string|max:50',
            'packages.*.burst_threshold' => 'nullable|string|max:50',
            'packages.*.burst_time' => 'nullable|string|max:50',
            'packages.*.priority' => 'nullable|integer|min:1|max:8',
        ]);

        foreach ($request->packages as $item) {
            InternetPackage::updateOrCreate(
                ['mikrotik_profile' => $item['mikrotik_profile']],
                [
                    'name' => $item['name'],
                    'download_speed' => $item['download_speed'] ?? 0,
                    'upload_speed' => $item['upload_speed'] ?? 0,
                    'monthly_price' => $item['monthly_price'] ?? 0,
                    'burst_limit' => $item['burst_limit'] ?? null,
                    'burst_threshold' => $item['burst_threshold'] ?? null,
                    'burst_time' => $item['burst_time'] ?? null,
                    'priority' => $item['priority'] ?? 8,
                    'active' => true,
                ]
            );
        }

        return redirect()
            ->route('packages.index')
            ->with('success', 'Import paket berhasil.');
    }

    public function sync(InternetPackage $package, MikroTikService $mikrotik)
    {
        $routers = Router::where('active', true)->get();
        $success = 0;
        $failed = [];

        foreach ($routers as $router) {
            try {
                $mikrotik->syncProfile($router, [
                    'name' => $package->mikrotik_profile,
                    'download_speed' => $package->download_speed,
                    'upload_speed' => $package->upload_speed,
                    'burst_limit' => $package->burst_limit,
                    'burst_threshold' => $package->burst_threshold,
                    'burst_time' => $package->burst_time,
                    'priority' => $package->priority,
                ]);

                $success++;
            } catch (\Throwable $e) {
                $failed[] = $router->name . ': ' . $e->getMessage();
            }
        }

        if (count($failed) > 0) {
            return back()->withErrors([
                'sync' => 'Sebagian router gagal disinkronkan. ' . implode(' | ', $failed),
            ]);
        }

        return back()->with('success', "Paket berhasil disinkronkan ke {$success} router.");
    }
}
