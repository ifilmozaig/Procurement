<?php

namespace App\Filament\Resources\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
        {
            $totalUsers = User::count();
            $newUsers = User::where('created_at', '>=', now()->subDays(7))->count();
            
            return [
                Stat::make('Total Users', $totalUsers)
                    ->description('Total pengguna terdaftar')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->chart([7, 12, 15, 18, 22, 25, $totalUsers])
                    ->color('success'),
                
                Stat::make('User Baru (7 Hari)', $newUsers)
                    ->description('Pendaftar minggu ini')
                    ->descriptionIcon('heroicon-m-sparkles')
                    ->chart([1, 2, 1, 3, 2, 4, $newUsers])
                    ->color('warning'),
            ];
        }
}
