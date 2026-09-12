<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['invoice_id','method','amount','proof_path','status','notes','paid_at','verified_at'];
    protected $casts = ['amount'=>'decimal:2','paid_at'=>'datetime','verified_at'=>'datetime'];
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}