<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternetPackage extends Model
{
    protected $fillable = ['name','mikrotik_profile','download_speed','upload_speed','burst_limit','burst_threshold','burst_time','priority','monthly_price','active'];
    protected $casts = ['download_speed'=>'integer','upload_speed'=>'integer','priority'=>'integer','monthly_price'=>'decimal:2','active'=>'boolean'];
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
}