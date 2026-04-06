<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardHRGAStats extends BaseWidget
{
    protected static bool $isDiscovered = false;
    public static function canView(): bool
        {
            return auth()->user()->hasRole(['hrga','super_admin']);
        }

    public function getStats(): array
        {
            $approvedCount = Procurement::where('status', 'APPROVED')->count();
            $completedCount = Procurement::where('status', 'COMPLETED')->count();
            $rejectedCount = Procurement::where('status', 'REJECTED')->count();
            $opexCount = Procurement::whereIn('status', ['APPROVED', 'COMPLETED'])
                ->where('type', 'OPEX')
                ->count();
            
            $capexCount = Procurement::whereIn('status', ['APPROVED', 'COMPLETED'])
                ->where('type', 'CAPEX')
                ->count();
            
            $cashAdvanceCount = Procurement::whereIn('status', ['APPROVED', 'COMPLETED'])
                ->where('type', 'CASH_ADVANCE')
                ->count();

            return [
                Stat::make('Approved', $approvedCount)
                    ->description('Menunggu bukti pembayaran')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning'),

                Stat::make('Completed', $completedCount)
                    ->description('Bukti pembayaran diunggah')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('success'),

                Stat::make('Rejected', $rejectedCount) 
                    ->description('Pengadaan yang ditolak')
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger'),

                Stat::make('OPEX', $opexCount)
                    ->description('Operating Expenditure')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('info'),

                Stat::make('CAPEX', $capexCount)
                    ->description('Capital Expenditure')
                    ->descriptionIcon('heroicon-o-building-office')
                    ->color('warning'),

                Stat::make('CASH ADVANCE', $cashAdvanceCount)
                    ->description('Cash Advance Urgent')
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('success'),
            ];
        }
}
