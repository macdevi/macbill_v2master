<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $fillable = [
        'submission_key', 'receipt_number', 'customer_id', 'area_id',
        'created_by', 'customer_name', 'customer_phone', 'customer_address',
        'package_name', 'activated_on', 'first_billing_on', 'billing_day',
        'monthly_price', 'tax_mode', 'installation_fee', 'equipment_fee',
        'other_fee', 'total_amount', 'paid_amount', 'payment_method',
        'paid_at', 'notes',
    ];

    protected $casts = [
        'activated_on' => 'date',
        'first_billing_on' => 'date',
        'paid_at' => 'datetime',
        'monthly_price' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'equipment_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
