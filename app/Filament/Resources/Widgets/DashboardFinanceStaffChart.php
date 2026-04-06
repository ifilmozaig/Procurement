<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Widgets\ChartWidget;

class DashboardFinanceStaffChart extends ChartWidget
{
    protected static bool $isDiscovered = false;
    public static function canView(): bool
        {
            return auth()->user()->hasRole(['finance', 'finance_manager','super_admin']);
        }

    public function getHeading(): ?string
        {
            return 'Procurement by Type';
        }

    public function getData(): array
        {
            $opexCount = Procurement::where('type', 'OPEX')->count();
            $capexCount = Procurement::where('type', 'CAPEX')->count();
            $cashAdvanceCount = Procurement::where('type', 'CASH_ADVANCE')->count();

            return [
                'datasets' => [
                    [
                        'label' => 'Total Procurement',
                        'data' => [$opexCount, $capexCount, $cashAdvanceCount],
                        'backgroundColor' => [
                            'rgb(59, 130, 246)',    
                            'rgb(234, 179, 8)',     
                            'rgb(34, 197, 94)',     
                        ],
                    ],
                ],
                'labels' => ['OPEX', 'CAPEX', 'Cash Advance'],
            ];
        }

    public function getType(): string
        {
            return 'doughnut';
        }
}
