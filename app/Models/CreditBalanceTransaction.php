<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditBalanceTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'type',
        'balance_before',
        'amount',
        'balance_after',
        'reason',
    ];

    protected $casts = [
        'balance_before' => 'decimal:2',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
