<?php
namespace App\Filament\Resources\Procurements\Pages;

use Filament\Pages\Page;
class DashboardFinanceStaff extends Page
{
    protected string $view = 'admin.dashboard-finance-staff';
    protected static string $routePath = 'dashboard-finance-staff';
    public static function canAccess(): bool
        {
            return auth()->user()->hasRole(['finance','super_admin']);
        }

    public static function shouldRegisterNavigation(): bool
        {
            return auth()->check() && auth()->user()->hasRole(['finance','super_admin']);
        }

    public static function getNavigationIcon(): ?string
        {
            return 'heroicon-o-currency-dollar'; 
        }

    public static function getNavigationLabel(): string
        {
            return 'Finance Dashboard';
        }

    public static function getNavigationSort(): ?int
        {
            return 1;
        }

    public function getTitle(): string
        {
            return 'Finance Dashboard';
        }

    public function getHeaderWidgets(): array
        {
            return [
                \App\Filament\Resources\Widgets\DashboardFinanceStaffStats::class,
                // \App\Filament\Resources\Widgets\DashboardFinanceStaffChart::class,
            ];
        }

    public function getFooterWidgets(): array
        {
            return [
                \App\Filament\Resources\Widgets\DashboardFinanceStaffTable::class,
                \App\Filament\Resources\Widgets\ProcurementHistoryWidget::class,
            ];
        }

    public function getHeaderWidgetsColumns(): int | array
        {
            return 2;
        }
    
    public function getFooterWidgetsColumns(): int | array
        {
            return 1;
        }
}
