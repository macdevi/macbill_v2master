<?php
namespace App\Http\Controllers;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index(Request $request)
    {
        $routers = Router::latest()->get();
        $resourceChecks = [];
        foreach ($routers as $router) {
            $check = $request->session()->get('router_resource_checks.'.$router->id);
            if (is_array($check) && hash_equals(
                $this->routerResourceSignature($router),
                (string) ($check['signature'] ?? '')
            )) {
                $resourceChecks[$router->id] = $check;
            }
        }
        return view('routers.index', compact('routers', 'resourceChecks'));
    }
    public function create()
    {
        return view('routers.create',[
            'areas'=>\App\Models\Area::where('active',true)->orderBy('name')->get(),
        ]);
    }
    public function store(Request $r)
    {
        $data=$this->validateRouterAccessForm($r);
        $areaIds=$data['area_ids']??[];
        unset($data['area_ids'],$data['area_access_present']);
        $data['ssl']=$r->boolean('ssl');
        \Illuminate\Support\Facades\DB::transaction(function()use($data,$areaIds){
            $router=Router::create($data);
            $router->accessibleAreas()->sync($areaIds);
        });
        return redirect()->route('routers.index')->with('success','Router dan akses wilayah ditambahkan. Perangkat MikroTik tidak diubah.');
    }
    public function edit(Router $router)
    {
        $router->load('accessibleAreas');
        return view('routers.create',[
            'router'=>$router,
            'areas'=>\App\Models\Area::query()->where(fn($q)=>$q->where('active',true)
                ->orWhereIn('id',$router->accessibleAreas->pluck('id')))->orderBy('name')->get(),
        ]);
    }
    public function update(Request $r,Router $router)
    {
        $data=$this->validateRouterAccessForm($r,$router);
        $areaIds=array_map('intval',$data['area_ids']??[]);
        unset($data['area_ids'],$data['area_access_present']);
        if(empty($data['password'])) unset($data['password']);
        $data['ssl']=$r->boolean('ssl');
        \Illuminate\Support\Facades\DB::transaction(function()use($router,$data,$areaIds){
            $removed=$router->accessibleAreas()->pluck('areas.id')->map(fn($id)=>(int)$id)->diff($areaIds);
            if($removed->isNotEmpty() && $router->customers()->whereIn('area_id',$removed)->exists()){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'area_ids'=>'Wilayah tidak dapat dilepas karena masih ada pelanggan pada router ini di wilayah tersebut.',
                ]);
            }
            $router->update($data);
            $router->accessibleAreas()->sync($areaIds);
        });
        return redirect()->route('routers.index')->with('success','Router dan akses wilayah diperbarui. Perangkat MikroTik tidak diubah.');
    }

    private function validateRouterAccessForm(Request $r,?Router $router=null): array
    {
        $retained=$router ? $router->accessibleAreas()->pluck('areas.id')->all() : [];
        return $r->validate([
            'name'=>['required','string','max:100'],
            'host'=>['required','string','max:255'],
            'port'=>['required','integer','min:1','max:65535'],
            'username'=>['required','string','max:100'],
            'password'=>[$router?'nullable':'required','string'],
            'ssl'=>['nullable','boolean'],
            'isolation_profile'=>['nullable','string','max:100'],
            'area_access_present'=>['required','accepted'],
            'area_ids'=>['sometimes','array'],
            'area_ids.*'=>['required','integer','distinct',
                \Illuminate\Validation\Rule::exists('areas','id')->where(fn($q)=>$q->where(fn($allowed)=>$allowed
                    ->where('active',true)->orWhereIn('id',$retained))),
            ],
        ],[
            'area_access_present.required'=>'Bagian akses wilayah harus disertakan. Muat ulang form router.',
            'area_ids.*.exists'=>'Wilayah tidak tersedia atau sudah nonaktif.',
        ]);
    }

    public function destroy(Request $request, Router $router)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        $data = $request->validate([
            'confirm_name' => ['required', 'string', 'max:100'],
            'acknowledge' => ['accepted'],
            'fingerprint' => ['required', 'regex:/\A[a-f0-9]{64}\z/'],
            'approval' => ['required', 'regex:/\A[a-f0-9]{64}\z/'],
            'expires' => ['required', 'integer'],
        ]);
        $expected = hash_hmac('sha256', implode('|', [
            $router->id, $request->user()->id,
            $data['expires'], $data['fingerprint'],
        ]), (string) config('app.key'));
        if (!hash_equals($expected, $data['approval'])
            || (int) $data['expires'] < time()
            || (int) $data['expires'] > time() + 600) {
            return redirect()->route('routers.delete-preview', $router)->withErrors([
                'router' => 'Konfirmasi tidak valid atau kedaluwarsa. Tinjau preview baru.',
            ]);
        }
        if (!hash_equals((string) $router->name, $data['confirm_name'])) {
            return redirect()->route('routers.delete-preview', $router)->withErrors([
                'router' => 'Nama konfirmasi harus sama persis dengan nama router.',
            ]);
        }
        try {
            $result = $this->routerDeletionProcess($router, [
                'mode' => 'execute',
                'fingerprint' => $data['fingerprint'],
                'confirm_name' => $data['confirm_name'],
                'actor_id' => $request->user()->id,
                'backup_dir' => storage_path('app/router-delete-private'),
            ]);
            if (!empty($result['stale']) || !empty($result['name_mismatch'])) {
                return redirect()->route('routers.delete-preview', $router)->withErrors([
                    'router' => 'Data berubah sejak preview. Tidak dihapus. Tinjau ulang.',
                ]);
            }
            if (empty($result['committed']) || empty($result['backup_id'])) {
                throw new \RuntimeException('Hasil eksekusi tidak dapat dipastikan.');
            }
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('routers.index')->withErrors([
                'router' => 'Eksekusi tidak terkonfirmasi. Jangan ulangi penghapusan. '
                    .'Periksa log, keberadaan router, dan receipt backup terlebih dahulu.',
            ]);
        }
        try {
            \Illuminate\Support\Facades\Log::info('Router deletion committed', [
                'router_id' => $router->id,
                'actor_id' => $request->user()->id,
                'backup_id' => $result['backup_id'],
                'counts' => $result['counts'],
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
        return redirect()->route('routers.index')->with('success',
            'Router dan data sasarannya sudah dihapus. Backup ID: '
            .$result['backup_id'].'. Perangkat MikroTik tidak diubah.'
        );
    }
    public function deletePreview(Request $request, Router $router)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        try {
            $plan = $this->routerDeletionProcess($router, ['mode' => 'preview']);
            $expires = time() + 600;
            $approval = hash_hmac('sha256', implode('|', [
                $router->id, $request->user()->id, $expires, $plan['fingerprint'],
            ]), (string) config('app.key'));
            return view('routers.delete-preview', compact(
                'router', 'plan', 'expires', 'approval'
            ));
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('routers.index')->withErrors([
                'router' => 'Preview gagal. Tidak ada penghapusan dijalankan. Periksa log.',
            ]);
        }
    }
    public function test(Router $router,MikroTikService $m){try{$m->testConnection($router);return back()->with('success','Koneksi MikroTik berhasil.');}catch(\Throwable $e){return back()->withErrors(['router'=>'Koneksi gagal: '.$e->getMessage()]);}}

    private function routerDeletionProcess(Router $router, array $payload): array
    {
        $connection = $router->getConnection();
        if ($connection->getDriverName() !== 'sqlite') {
            throw new \RuntimeException('Database bukan SQLite.');
        }
        $database = false;
        foreach ($connection->select('PRAGMA database_list') as $attached) {
            if ($attached->name === 'main' && !empty($attached->file)) {
                $database = realpath($attached->file);
                break;
            }
        }
        if (!$database || !is_file($database)) {
            throw new \RuntimeException('Database aktif tidak ditemukan.');
        }
        $process = new \Symfony\Component\Process\Process([
            'python3', app_path('Support/router_delete_final.py'),
            $database, (string) $router->id,
        ]);
        $process->setInput(json_encode($payload, JSON_THROW_ON_ERROR));
        $process->setTimeout(120);
        $process->mustRun();
        $result = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($result)
            || (empty($result['ok']) && empty($result['stale'])
                && empty($result['name_mismatch']))) {
            throw new \RuntimeException('Respons penghapusan tidak valid.');
        }
        return $result;
    }

    private function routerResourceSignature(Router $router): string
    {
        return hash('sha256', json_encode([
            $router->id, $router->host, $router->port, $router->username,
            $router->ssl, $router->active,
            $router->getRawOriginal('password'),
            $router->getRawOriginal('updated_at'),
        ], JSON_THROW_ON_ERROR));
    }

    public function connectionStatus(Request $request, Router $router, MikroTikService $mikrotik)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        if (!$router->active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false, 'message' => 'Router nonaktif. Pemeriksaan tidak dijalankan.',
                ], 422);
            }
            return redirect()->route('routers.index')->withErrors([
                'router' => 'Router nonaktif. Pemeriksaan tidak dijalankan.',
            ]);
        }
        $check = [
            'signature' => $this->routerResourceSignature($router),
            'state' => 'failed', 'uptime' => null, 'cpu_load' => null,
        ];
        try {
            $resource = $mikrotik->systemResource($router);
            if (!is_array($resource)) {
                throw new \RuntimeException('Respons system resource kosong.');
            }
            MikroTikService::assertSuccessfulResponse([$resource]);
            $uptime = trim((string) ($resource['uptime'] ?? ''));
            $cpu = rtrim(trim((string) ($resource['cpu-load'] ?? '')), '%');
            if ($uptime === '' || strlen($uptime) > 100 || !is_numeric($cpu)
                || (float) $cpu < 0 || (float) $cpu > 100) {
                throw new \RuntimeException('Respons uptime atau CPU tidak valid.');
            }
            $check['state'] = 'success';
            $check['uptime'] = $uptime;
            $check['cpu_load'] = (float) $cpu;
        } catch (\Throwable $exception) {
            report($exception);
        }
        $checked = now();
        $check['checked_timestamp'] = $checked->timestamp;
        $check['checked_display'] = $checked->format('d/m/Y H:i:s');
        $check['timezone'] = config('app.timezone', 'UTC');
        $request->session()->put('router_resource_checks.'.$router->id, $check);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'check' => $check])
                ->header('Cache-Control', 'no-store, private');
        }
        if ($check['state'] !== 'success') {
            return redirect()->route('routers.index')->withErrors([
                'router' => 'Tes koneksi '.$router->name.' gagal membaca resource. Periksa log.',
            ]);
        }
        return redirect()->route('routers.index')->with('success',
            'Tes koneksi '.$router->name.' berhasil. Uptime dan CPU diperbarui.'
        );
    }
}