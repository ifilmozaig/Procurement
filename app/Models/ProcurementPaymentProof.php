<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementPaymentProof extends Model
{
    protected $fillable = [
        'procurement_id',
        'company_id',
        'payment_proof',
        'realisasi_amount',
    ];

    protected $casts = [
        'realisasi_amount' => 'integer',
    ];

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}