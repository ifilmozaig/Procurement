<?php

namespace App\Filament\Resources\Procurements\Pages;
use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static ?string $title = '';

    public function mount(): void
    {
        if (auth()->check() && auth()->user()->hasRole('accounting')) {
            redirect()->to(\App\Filament\Resources\MasterData\ExpenseMasterResource::getUrl('realisasi'))->send();
            exit;
        }
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public function getView(): string
    {
        return 'filament.pages.custom-dashboard';
    }
}