<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'business_type',
        'address',
        'city',
        'phone',
        'email',
        'pic_name',
        'payment_methods',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'payment_methods' => 'array',
    ];

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(VendorBankAccount::class)->orderByDesc('is_primary');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('name');
    }

    // Label lengkap untuk dropdown: "[PT] Nama Vendor"
    public function getFullLabelAttribute(): string
    {
        $prefix = $this->business_type ? "[{$this->business_type}] " : '';
        return $prefix . $this->name;
    }
}