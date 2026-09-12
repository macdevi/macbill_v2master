<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{

    public function index()
    {
        $income = Payment::where('status','verified')->sum('amount');
        $expense = Expense::posted()->sum('amount');

        return view('dashboard',[
            'income'=>$income,
            'expense'=>$expense,
            'balance'=>$income-$expense,
            'customers'=>Customer::count(),
            'isolated'=>Customer::where('status','isolated')->count(),
            'unpaid'=>Invoice::whereIn('status',['unpaid','isolated'])->sum('amount'),
            'logs'=>ActivityLog::latest()->limit(10)->get(),
        ]);
    }


    public function pppoe(MikroTikService $mikrotik): JsonResponse
    {
        $all=[];
        $online=[];
        $errors=[];

        foreach(Router::where('active',true)->get() as $router){

            try {

                $secrets=$mikrotik->allPppoeUsers($router);

                foreach($secrets as $s){

                    if(($s['service'] ?? '') !== 'pppoe'){
                        continue;
                    }

                    $all[$s['name']] = [
                        'router'=>$router->name,
                        'username'=>$s['name'],
                        'profile'=>$s['profile'] ?? '-',
                        'status'=>'offline'
                    ];
                }


                $active=$mikrotik->onlineUsers($router);

                foreach($active as $a){

                    $name=$a['name'] ?? '';

                    $online[$name]=[
                        'router'=>$router->name,
                        'username'=>$name,
                        'address'=>$a['address'] ?? '-',
                        'uptime'=>$a['uptime'] ?? '-',
                        'status'=>'online'
                    ];

                    if(isset($all[$name])){
                        $all[$name]=$online[$name];
                    }
                }


            } catch(\Throwable $e){

                $errors[]=[
                    'router'=>$router->name,
                    'message'=>$e->getMessage()
                ];

            }
        }


        $allUsers=array_values($all);

        $onlineUsers=array_values($online);

        $offlineUsers=array_values(
            array_filter($allUsers,function($u){
                return ($u['status'] ?? '') === 'offline';
            })
        );


        return response()->json([
            'total'=>count($allUsers),
            'online'=>count($onlineUsers),
            'offline'=>count($offlineUsers),
            'users'=>$allUsers,
            'online_users'=>$onlineUsers,
            'offline_users'=>$offlineUsers,
            'errors'=>$errors
        ]);
    }


}
