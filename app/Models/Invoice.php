<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'billing_period',
        'service_period_start',
        'service_period_end',
        'is_prorated',
        'charged_days',
        'cycle_days',
        'monthly_price_snapshot',
        'invoice_number',
        'billing_date',
        'due_date',
        'service_amount',
        'subtotal',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'tax_mode',
        'gross_amount',
        'credit_used',
        'amount',
        'status',
    ];

    protected $casts = [
        'billing_period' => 'date',
        'service_period_start' => 'date',
        'service_period_end' => 'date',
        'is_prorated' => 'boolean',
        'charged_days' => 'integer',
        'cycle_days' => 'integer',
        'monthly_price_snapshot' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'service_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'credit_used' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}