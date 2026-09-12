<?php
namespace App\Http\Controllers;
use App\Models\Router;
use App\Services\MikroTikService;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index(){return view('routers.index',['routers'=>Router::latest()->get()]);}
    public function create(){return view('routers.create');}
    public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:100','host'=>'required|string|max:255','port'=>'required|integer|min:1|max:65535','username'=>'required|string|max:100','password'=>'required|string','ssl'=>'nullable|boolean']);$d['ssl']=$r->boolean('ssl');Router::create($d);return redirect()->route('routers.index')->with('success','Router ditambahkan.');}
    public function edit(Router $router){return view('routers.create',compact('router'));}
    public function update(Request $r,Router $router){$d=$r->validate(['name'=>'required|string|max:100','host'=>'required|string|max:255','port'=>'required|integer|min:1|max:65535','username'=>'required|string|max:100','password'=>'nullable|string','ssl'=>'nullable|boolean']);if(!$d['password'])unset($d['password']);$d['ssl']=$r->boolean('ssl');$router->update($d);return redirect()->route('routers.index')->with('success','Router diperbarui.');}
    public function destroy(Router $router){$router->delete();return back()->with('success','Router dihapus.');}
    public function test(Router $router,MikroTikService $m){try{$m->testConnection($router);return back()->with('success','Koneksi MikroTik berhasil.');}catch(\Throwable $e){return back()->withErrors(['router'=>'Koneksi gagal: '.$e->getMessage()]);}}
}