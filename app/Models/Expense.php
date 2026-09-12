<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const CATEGORIES = [
        'Operasional',
        'Gaji & Honor',
        'Utilitas',
        'Internet & Infrastruktur',
        'Perangkat & Perawatan',
        'Transportasi',
        'Sewa',
        'Pajak & Administrasi',
        'Pemasaran',
        'Lainnya',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Tunai',
        'bank_transfer' => 'Transfer Bank',
        'e_wallet' => 'E-Wallet',
        'other' => 'Lainnya',
    ];

    protected $fillable = [
        'created_by',
        'title',
        'category',
        'description',
        'vendor',
        'amount',
        'payment_method',
        'status',
        'voided_at',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'voided_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? 'Lainnya';
    }
}
