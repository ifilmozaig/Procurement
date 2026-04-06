<?php

namespace App\Filament\Resources\Procurements\Support;

class ProcurementStatus
{
    public static function color(string $state): string
    {
        return match ($state) {
            'DRAFT'      => 'gray',
            'PENDING'    => 'warning',
            'PROCESSING' => 'info',
            'APPROVED'   => 'success',
            'COMPLETED'  => 'primary',
            'REJECTED'   => 'danger',
            default      => 'gray',
        };
    }

    public static function label(string $state): string
    {
        return match ($state) {
            'DRAFT'      => 'Draft',
            'PENDING'    => 'Menunggu Review',
            'PROCESSING' => 'Diproses Manager',
            'APPROVED'   => 'Disetujui',
            'COMPLETED'  => 'Selesai',
            'REJECTED'   => 'Ditolak',
            default      => $state,
        };
    }
}
