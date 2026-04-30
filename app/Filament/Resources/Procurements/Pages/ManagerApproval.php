<?php
namespace App\Filament\Resources\Procurements\Pages;

use App\Filament\Resources\Procurements\Support\ProcurementStatus;
use App\Models\Procurement;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ManagerApproval extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'admin.manager-approval';
    public static function getRoutePath(Panel $panel): string
        {
            return 'manager-approval';
        }

    public static function canAccess(): bool
        {
            return auth()->user()->hasRole(['finance_manager', 'atasan_finance', 'manager']);
        }

    public static function shouldRegisterNavigation(): bool
        {
            return false;
        }

    public static function getNavigationIcon(): ?string
        {
            return 'heroicon-o-shield-check';
        }

    public static function getNavigationLabel(): string
        {
            return 'Manager Approval';
        }

    public static function getNavigationSort(): ?int
        {
            return 2;
        }

    public function getTitle(): string
        {
            return 'Manager Approval - CAPEX & Cash Advance';
        }

    public function table(Table $table): Table
        {
            return $table
                ->query(
                    Procurement::query()
                        ->where('status', 'PROCESSING')
                        ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
                        ->latest()
                )
                ->columns([
                    Tables\Columns\TextColumn::make('procurement_number')
                        ->label('Procurement Number')
                        ->searchable()
                        ->sortable()
                        ->copyable()
                        ->color('primary')
                        ->weight('bold')
                        ->url(fn (Procurement $record): string => 
                            route('filament.admin.resources.procurements.view', ['record' => $record->id])
                        ),

                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Requester')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'CAPEX' => 'warning',
                            'CASH_ADVANCE' => 'success',
                            default => 'gray',
                        }),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => ProcurementStatus::color($state))
                        ->formatStateUsing(fn (string $state): string => ProcurementStatus::label($state)),

                    Tables\Columns\TextColumn::make('reason')
                        ->label('Reason')
                        ->limit(40)
                        ->tooltip(fn ($record) => $record->reason),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Created')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('type')
                        ->options([
                            'CAPEX' => 'CAPEX',
                            'CASH_ADVANCE' => 'Cash Advance',
                        ])
                        ->label('Type'),
                ])
                ->recordUrl(fn (Procurement $record): string => 
                    route('filament.admin.resources.procurements.view', ['record' => $record->id])
                )
                ->defaultSort('created_at', 'desc')
                ->emptyStateHeading('Tidak ada procurement yang perlu approval')
                ->emptyStateDescription('Belum ada CAPEX atau Cash Advance yang perlu disetujui Manager.')
                ->poll('30s');
        }
}
