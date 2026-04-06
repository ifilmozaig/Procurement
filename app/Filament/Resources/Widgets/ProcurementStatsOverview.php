<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProcurementStatsOverview extends BaseWidget
{
    protected function getStats(): array
        {
            $userId = auth()->id();
            
            return [
                Stat::make('Total Requests', Procurement::where('user_id', $userId)->count())
                    ->description('All procurement requests')
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('primary'),
                
                Stat::make('Pending Approval', Procurement::where('user_id', $userId)->where('status', 'PENDING')->count())
                    ->description('Waiting for review')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning'),
                
                Stat::make('Approved', Procurement::where('user_id', $userId)->where('status', 'APPROVED')->count())
                    ->description('Approved requests')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
            ];
        }
}
