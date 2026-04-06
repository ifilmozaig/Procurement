<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementAttachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'procurement_id',
        'file_name',
        'file_path',
        'file_type',
        'attachment_type',
    ];

    protected $touches = ['procurement'];
    public function procurement(): BelongsTo
        {
            return $this->belongsTo(Procurement::class);
        }
}