<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Procurement extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id', 
        'user_id',
        'type',
        'procurement_number',
        'reason',
        'status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'completed_at',       
    
        'reviewed_by',
        'reviewed_at',
        
        'finance_comment',
        'forwarded_by',
        'forwarded_at',
        
        'approved_by_manager',
        'manager_approved_at',
        'manager_comment',
        
        'payment_proof',
        'payment_proof_uploaded_at',
        'realisasi_amount',
        'payment_proof_konnco',
        'payment_proof_kodemee',
        'realisasi_amount_konnco',
        'realisasi_amount_kodemee',

        'revision_note',
        'revised_by',
        'revised_at',
    ];

    protected $casts = [
        'submitted_at'              => 'datetime',
        'approved_at'               => 'datetime',
        'rejected_at'               => 'datetime',
        'completed_at'              => 'datetime',  
        'reviewed_at'               => 'datetime',
        'forwarded_at'              => 'datetime',
        'manager_approved_at'       => 'datetime',
        'payment_proof_uploaded_at' => 'datetime',
        'revised_at'                => 'datetime',
        'realisasi_amount'          => 'integer',
        'realisasi_amount_konnco'   => 'integer',
        'realisasi_amount_kodemee'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProcurementAttachment::class);
    }

    public function paymentProofs(): HasMany 
    {
    return $this->hasMany(ProcurementPaymentProof::class);
    }
    
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function forwardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function managerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function revisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($procurement) {
            $procurement->id = self::getNextAvailableId();
            if (!$procurement->procurement_number) {
                $procurement->procurement_number = self::generateProcurementNumber($procurement->type);
            }
        });

        static::deleting(function ($procurement) {
            $procurement->items()->delete();
            foreach ($procurement->attachments as $attachment) {
                if ($attachment->file_path && \Storage::disk('public')->exists($attachment->file_path)) {
                    \Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
            if ($procurement->payment_proof && \Storage::disk('public')->exists($procurement->payment_proof)) {
                \Storage::disk('public')->delete($procurement->payment_proof);
            }
            if ($procurement->payment_proof_konnco && \Storage::disk('public')->exists($procurement->payment_proof_konnco)) {
                \Storage::disk('public')->delete($procurement->payment_proof_konnco);
            }
            if ($procurement->payment_proof_kodemee && \Storage::disk('public')->exists($procurement->payment_proof_kodemee)) {
                \Storage::disk('public')->delete($procurement->payment_proof_kodemee);
            }
        });
    }

    private static function getNextAvailableId(): int
    {
        $usedIds = DB::table('procurements')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (empty($usedIds)) {
            return 1;
        }
    
        $expectedId = 1;
        foreach ($usedIds as $currentId) {
            if ($currentId != $expectedId) {
                return $expectedId;
            }
            $expectedId++;
        }
        return $expectedId;
    }

    private static function generateProcurementNumber(string $type): string
    {
        $prefix = match($type) {
            'OPEX'         => 'OPX',
            'CAPEX'        => 'CPX',
            'CASH_ADVANCE' => 'CA',
            default        => 'PCM',
        };

        $date = now()->format('Ymd');
        return DB::transaction(function () use ($prefix, $date) {
            $lastNumber = DB::table('procurements')
                ->whereDate('created_at', now()->toDateString())
                ->where('procurement_number', 'like', "%-{$date}-%")
                ->lockForUpdate()
                ->orderByDesc('procurement_number')
                ->value('procurement_number');

            if ($lastNumber) {
                $lastSeq = (int) substr($lastNumber, -4);
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }

            $candidate = sprintf('%s-%s-%04d', $prefix, $date, $nextSeq);
            while (DB::table('procurements')->where('procurement_number', $candidate)->exists()) {
                $nextSeq++;
                $candidate = sprintf('%s-%s-%04d', $prefix, $date, $nextSeq);
            }

            return $candidate;
        });
    }

    public function isOpex(): bool
    {
        return $this->type === 'OPEX';
    }

    public function isCapex(): bool
    {
        return $this->type === 'CAPEX';
    }

    public function isCashAdvance(): bool
    {
        return $this->type === 'CASH_ADVANCE';
    }

    public function needsManagerApproval(): bool
    {
        return in_array($this->type, ['CAPEX', 'CASH_ADVANCE']);
    }

    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof);
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    public function isCompleted(): bool        
    {
        return $this->status === 'COMPLETED';
    }

    public function markAsCompleted(): void    
    {
        $this->update([
            'status'       => 'COMPLETED',
            'completed_at' => now(),
        ]);
    }
}