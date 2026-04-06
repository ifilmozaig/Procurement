<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseMasterItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_category_id',
        'item_name',
        'specification',
        'unit',
        'estimated_price',
        'vendor',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($item) {
            $item->sort_order = static::where('expense_category_id', $item->expense_category_id)
                ->max('sort_order') + 1;
        });

        static::saving(function ($item) {
            if (is_null($item->sort_order) || $item->sort_order === '') {
                $item->sort_order = 0;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function budgetPlans(): HasMany
    {
        return $this->hasMany(ExpenseBudgetPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getFullNameAttribute(): string
    {
        return ($this->category->name ?? '') . ' - ' . $this->item_name;
    }
}