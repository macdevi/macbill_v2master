<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'router_id',
        'area_id',
        'internet_package_id',
        'monthly_price_override',
        'tax_mode',
        'customer_code',
        'name',
        'phone',
        'address',
        'pppoe_username',
        'pppoe_password',
        'due_day',
        'status',
        'credit_balance',
    ];

    protected $casts = [
        'due_day' => 'integer',
        'monthly_price_override' => 'decimal:2',
        'credit_balance' => 'decimal:2',
    ];

    protected $hidden = [
        'pppoe_password',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function internetPackage(): BelongsTo
    {
        return $this->belongsTo(InternetPackage::class);
    }

    public function getEffectiveMonthlyPriceAttribute(): float
    {
        if ($this->monthly_price_override !== null) {
            return (float) $this->monthly_price_override;
        }

        return (float) ($this->internetPackage?->monthly_price ?? 0);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
