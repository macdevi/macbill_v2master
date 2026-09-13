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
}
