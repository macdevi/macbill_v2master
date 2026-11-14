<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Router extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'host',
        'port',
        'username',
        'password',
        'ssl',
        'active',
        'isolation_profile',
    ];

    protected $casts = [
        'ssl' => 'boolean',
        'active' => 'boolean',
        'port' => 'integer',
        'password' => 'encrypted',
    ];

    protected $hidden = [
        'password',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function internetPackages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            InternetPackage::class,
            'internet_package_router',
            'router_id',
            'internet_package_id'
        )->withTimestamps();
    }


    public function accessibleAreas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Area::class,'area_router_access','router_id','area_id')->withTimestamps();
    }

}
