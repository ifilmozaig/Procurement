<?php
namespace App\Filament\Resources\Procurements\Pages;

use Filament\Pages\Page;
use Filament\Panel;

class DashboardManager extends Page
{
    protected string $view = 'admin.dashboard-manager';
    public static function getRoutePath(Panel $panel): string
        {
            return 'dashboard-manager';
        }

    public static function canAccess(): bool
        {
            return auth()->check() && auth()->user()->hasRole(['finance_manager','super_admin']);
        }

    public static function shouldRegisterNavigation(): bool
        {
            return auth()->check() && auth()->user()->hasRole(['finance_manager','super_admin']);
        }

    public static function getNavigationIcon(): ?string
        {
            return 'heroicon-o-clipboard-document-check';
        }

    public static function getNavigationLabel(): string
        {
            return 'Manager Dashboard';
        }

    public static function getNavigationSort(): ?int
        {
            return 1;
        }

    public function getTitle(): string
        {
            return 'Manager Dashboard';
        }

    public function getWidgets(): array
        {
            return [
                \App\Filament\Resources\Widgets\DashboardManagerStats::class,
                \App\Filament\Resources\Widgets\ManagerProcurementTableWidget::class,
            ];
        }
}
