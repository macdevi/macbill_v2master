<?php
namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class DashboardNetworkService
{
    public function snapshot(mixed $requestedRouterId, object $reader): array
    {
        $user=auth()->user();
        abort_unless($user && in_array($user->role,['super_admin','admin'],true),403);
        $routers=CustomerAccessScope::routers()->orderBy('id')->get();
        if($requestedRouterId!==null && $requestedRouterId!==''){
            abort_unless(is_int($requestedRouterId)||is_string($requestedRouterId),422,'Pilihan router tidak valid.');
            $id=filter_var($requestedRouterId,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
            abort_unless($id!==false,422,'Pilihan router tidak valid.');
            $router=$routers->first(fn($row)=>(int)$row->id===$id);
            abort_unless($router,403,'Router tidak tersedia dalam akses Anda.');
        }else{
            $router=$routers->first();
        }
        $connected='Disconnected';
        $cpu=null;
        $uptime=null;
        $identity=null;
        $traffic=['interface'=>'ether1','download_bps'=>null,'upload_bps'=>null,'download'=>'-','upload'=>'-'];
        $known=false;
        $onlineByRouter=[];
        if($router){
            try{
                $resource=$reader->systemResource($router);
                if(!is_array($resource) || !is_numeric($resource['cpu-load']??null)
                    || (float)$resource['cpu-load']<0 || (float)$resource['cpu-load']>100
                    || trim((string)($resource['uptime']??''))===''){
                    throw new \RuntimeException('Resource router tidak valid.');
                }
                $connected='Connected';
                $cpu=(float)$resource['cpu-load'];
                $uptime=(string)$resource['uptime'];
            }catch(\Throwable $e){$this->recordFailure((int)$router->id,'resource',$e);}
            if($connected==='Connected'){
                try{$identity=$reader->identity($router);}catch(\Throwable $e){$this->recordFailure((int)$router->id,'identity',$e);}
                try{
                    $value=$reader->trafficSummary($router,'ether1');
                    if(!is_array($value) || !isset($value['download'],$value['upload']))throw new \RuntimeException('Trafik tidak valid.');
                    $traffic=array_merge($traffic,$value);
                }catch(\Throwable $e){$this->recordFailure((int)$router->id,'traffic',$e);}
            }
            try{
                $sessions=$reader->onlineUsers($router);
                if(!is_iterable($sessions))throw new \RuntimeException('Daftar sesi tidak valid.');
                $names=[];
                foreach($sessions as $session){
                    if(!is_array($session) || isset($session['!trap']) || isset($session['!fatal']))throw new \RuntimeException('Respons sesi tidak valid.');
                    $name=mb_strtolower(trim((string)($session['name']??'')));
                    if($name!=='')$names[$name]=true;
                }
                $onlineByRouter[$router->id]=$names;
                $known=true;
            }catch(\Throwable $e){$this->recordFailure((int)$router->id,'pppoe',$e);}
        }
        $lists=['online'=>[],'offline'=>[],'isolated'=>[],'unknown'=>[]];
        $customers=$router
            ? CustomerAccessScope::customers(Customer::query())->where('router_id',$router->id)
                ->with('router:id,name')->orderBy('name')
                ->get(['id','area_id','router_id','customer_code','name','phone','pppoe_username','status'])
            : collect();
        foreach($customers as $customer){
            $username=mb_strtolower(trim((string)$customer->pppoe_username));
            $isOnline=$known && $username!=='' && isset($onlineByRouter[$customer->router_id][$username]);
            $status=$customer->status==='isolated'?'isolated':(!$known?'unknown':($isOnline?'online':'offline'));
            $lists[$status][]=[
                'id'=>$customer->id,'customer_code'=>$customer->customer_code,'name'=>$customer->name,
                'phone'=>$customer->phone,'pppoe_username'=>$customer->pppoe_username,
                'router_name'=>$customer->router?->name,
            ];
        }
        $total=$customers->count();
        return [
            'monitorRouters'=>$routers,'selectedMonitorRouter'=>$router,
            'mikrotikConnected'=>$connected,'mikrotikCpu'=>$cpu,'mikrotikUptime'=>$uptime,
            'mikrotikIdentity'=>$identity,'traffic'=>$traffic,
            'totalCustomers'=>$total,'onlineCustomers'=>count($lists['online']),
            'offlineCustomers'=>count($lists['offline']),'isolatedCustomers'=>count($lists['isolated']),
            'unknownCustomers'=>count($lists['unknown']),
            'onlinePercentage'=>$total>0?round(count($lists['online'])/$total*100,1):0,
            'offlinePercentage'=>$total>0?round(count($lists['offline'])/$total*100,1):0,
            'customerStatusLists'=>$lists,'networkCheckedAt'=>now()->format('d/m/Y H:i:s'),
        ];
    }

    private function recordFailure(int $routerId,string $stage,\Throwable $error): void
    {
        Log::warning('Dashboard belum berhasil membaca router pilihan.',[
            'router_id'=>$routerId,'stage'=>$stage,'exception_class'=>get_class($error),
        ]);
    }
}
