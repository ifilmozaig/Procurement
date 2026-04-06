<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;

class ProcurementItem extends Model
{
    use HasFactory;

    const MAX_ESTIMATED_PRICE = 9_999_999_999;

    protected $fillable = [
        'procurement_id',
        'expense_master_item_id',
        'item_name',
        'specification',
        'unit',
        'quantity',
        'estimated_price',
        'vendor',         // kolom string lama (fallback)
        'vendor_id',      // FK ke tabel vendors
        'payment_method',
        'bank_account_id',
        'company_id',     // FK lama (single) — tetap dipertahankan untuk backward compatibility
        'is_purchased',
        'purchased_at',
        'purchased_by',
        'purchase_notes',
    ];

    protected $casts = [
        'is_purchased' => 'boolean',
        'purchased_at' => 'datetime',
    ];

    protected $touches = ['procurement'];

    public function setEstimatedPriceAttribute($value): void
    {
        if ((float) $value > self::MAX_ESTIMATED_PRICE) {
            throw ValidationException::withMessages([
                'estimated_price' =>
                    'Estimated price maksimal adalah Rp ' .
                    number_format(self::MAX_ESTIMATED_PRICE, 0, ',', '.'),
            ]);
        }
        $this->attributes['estimated_price'] = $value;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(ExpenseMasterItem::class, 'expense_master_item_id');
    }

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    /**
     * Relasi FK tunggal lama (backward compatibility).
     * Diganti nama dari vendor() → vendorModel() untuk menghindari
     * konflik dengan kolom string 'vendor' di $fillable.
     */
    public function vendorModel(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(VendorBankAccount::class, 'bank_account_id');
    }

    /**
     * Relasi lama (single company) — backward compatibility.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * ✅ BARU: Relasi many-to-many untuk multi-perusahaan.
     * Gunakan $item->companies untuk mendapatkan semua perusahaan terpilih.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            'procurement_item_companies',
            'procurement_item_id',
            'company_id'
        )->withTimestamps();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    public function category(): ?ExpenseCategory
    {
        return $this->masterItem?->category;
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->masterItem?->category?->name ?? 'Lainnya';
    }

    public function getPurchaseStatusLabelAttribute(): string
    {
        if ($this->is_purchased) return '✅ Sudah Dibeli';
        if (in_array($this->procurement?->status, ['APPROVED', 'COMPLETED'])) {
            return '⏳ Belum Dibeli (Approved)';
        }
        return '🔴 Belum Dibeli';
    }

    /**
     * Label perusahaan — gabungkan semua nama dari relasi many-to-many.
     * Fallback ke relasi lama jika pivot kosong.
     */
    public function getCompanyTargetLabelAttribute(): string
    {
        // Prioritas: relasi many-to-many
        if ($this->relationLoaded('companies') && $this->companies->isNotEmpty()) {
            return $this->companies->pluck('name')->implode(', ');
        }

        // Fallback: relasi lama (single)
        return $this->company?->name ?? '-';
    }

    /**
     * Array company IDs dari relasi many-to-many (untuk form state).
     */
    public function getCompanyIdsAttribute(): array
    {
        return $this->companies()->pluck('companies.id')->toArray();
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'transfer' => '🏦 Transfer Bank',
            'cash'     => '💵 Tunai (Cash)',
            'cek'      => '📄 Cek',
            'giro'     => '📋 Giro',
            'lainnya'  => '🔖 Lainnya',
            default    => '-',
        };
    }

    public function getBankAccountInfoAttribute(): string
    {
        $acc = $this->bankAccount;
        if (!$acc) return '-';
        return $acc->bank_name
            . ' — ' . $acc->account_number
            . ' a/n ' . $acc->account_name
            . ($acc->is_primary ? ' ⭐' : '');
    }

    /**
     * Nama vendor: prioritaskan relasi FK, fallback ke kolom string lama.
     */
    public function getVendorNameAttribute(): string
    {
        return $this->vendorModel?->name ?? $this->getRawOriginal('vendor') ?? '-';
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->estimated_price;
    }
}