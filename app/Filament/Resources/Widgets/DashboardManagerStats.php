<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardManagerStats extends BaseWidget
{
    protected static bool $isDiscovered = false;
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['finance_manager', 'super_admin']);
    }

    public function getStats(): array
    {
        $pendingCount = Procurement::where('status', 'PROCESSING')
        ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
            ->count();

        $approvedCount = Procurement::where('status', 'APPROVED')
            ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
            ->count();

        $completedCount = Procurement::where('status', 'COMPLETED')
            ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
            ->count();

        $rejectedCount = Procurement::where('status', 'REJECTED')
            ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
            ->count();

        $capexCount = Procurement::whereIn('status', ['PROCESSING', 'APPROVED', 'COMPLETED', 'REJECTED'])
            ->where('type', 'CAPEX')
            ->count();

        $cashAdvanceCount = Procurement::whereIn('status', ['PROCESSING', 'APPROVED', 'COMPLETED', 'REJECTED'])
            ->where('type', 'CASH_ADVANCE')
            ->count();

        return [
            Stat::make('Pending Approval', $pendingCount)
                ->description('Membutuhkan Persetujuan Manajer')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Approved', $approvedCount)
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Completed', $completedCount)
                ->description('Bukti pembayaran diunggah')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('info'),

            Stat::make('Rejected', $rejectedCount)
                ->description('Ditolak oleh Manajer')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

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
