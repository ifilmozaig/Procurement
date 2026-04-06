<?php

namespace App\Filament\Resources\Procurements\Pages;
use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static ?string $title = '';
    public static function getNavigationLabel(): string
        {
            return 'Dashboard';
        }

    public function getView(): string
        {
            return 'filament.pages.custom-dashboard';
        }
}
