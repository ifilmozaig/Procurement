<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankAccount extends Model
{
    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_number',
        'account_name',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // Label untuk ditampilkan: "BCA — 1234567890 a/n Konnco Studio"
    public function getLabelAttribute(): string
    {
        return "{$this->bank_name} — {$this->account_number} a/n {$this->account_name}";
    }
}