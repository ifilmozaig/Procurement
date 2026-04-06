<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardFinanceStaffStats extends BaseWidget
{
    protected static bool $isDiscovered = false;

    public static function canView(): bool
        {
            return auth()->user()->hasRole(['finance', 'finance_manager','super_admin']);
        }

        public function getStats(): array
        {
            $pendingCount = Procurement::where('status', 'PENDING')->count();
            $approvedCount = Procurement::where('status', 'APPROVED')->count();
            $rejectedCount = Procurement::where('status', 'REJECTED')->count();
            $completedCount = Procurement::where('status', 'COMPLETED')->count();
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
                Stat::make('Pending Review', $pendingCount)
                    ->description('Menunggu persetujuan')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning'),

                Stat::make('Approved', $approvedCount)
                    ->description('Telah disetujui')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success'),

                Stat::make('Rejected', $rejectedCount)
                    ->description('Ditolak')
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger'),

                Stat::make('Completed', $completedCount)
                    ->description('Selesai diproses')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('info'),

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
                    ->color('danger'),
            ];
        }
}
