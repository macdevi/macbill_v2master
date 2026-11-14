<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if(Schema::hasTable('area_router_access')) throw new RuntimeException('Tabel akses wilayah-router sudah ada. Periksa sebelum melanjutkan.');
        Schema::create('area_router_access',function(Blueprint $table){
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['area_id','router_id']);
            $table->index('router_id');
        });
        DB::transaction(function(){
            $now=now();
            foreach(DB::table('routers')->whereNotNull('area_id')->get(['id','area_id']) as $router){
                if(!DB::table('areas')->where('id',$router->area_id)->exists()) throw new RuntimeException('Wilayah router lama tidak ditemukan.');
                DB::table('area_router_access')->insertOrIgnore(['area_id'=>$router->area_id,'router_id'=>$router->id,'created_at'=>$now,'updated_at'=>$now]);
            }
            foreach([[2,1],[3,1],[4,3]] as [$area,$router]){
                DB::table('area_router_access')->insertOrIgnore(['area_id'=>$area,'router_id'=>$router,'created_at'=>$now,'updated_at'=>$now]);
            }
        });
    }
    public function down(): void {Schema::dropIfExists('area_router_access');}
};
