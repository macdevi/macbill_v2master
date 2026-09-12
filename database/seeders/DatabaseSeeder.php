<?php
namespace Database\Seeders;
use App\Models\InternetPackage;
use App\Models\Router;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
 public function run():void {
  DB::table('users')->insert(['name'=>'Administrator','email'=>'admin@macbilling.local','password'=>Hash::make('ChangeMe123!'),'created_at'=>now(),'updated_at'=>now()]);
  InternetPackage::create(['name'=>'Paket 5 Mbps','mikrotik_profile'=>'profile-5m','download_speed'=>5,'upload_speed'=>5,'monthly_price'=>100000]);
  InternetPackage::create(['name'=>'Paket 10 Mbps','mikrotik_profile'=>'profile-10m','download_speed'=>10,'upload_speed'=>10,'monthly_price'=>150000]);
 }
}