<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProcurementHistoryWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = null;
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['finance', 'finance_manager', 'super_admin']);
    }

    protected function paginateTableQuery(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->getTableRecordsPerPage());
    }

    public function table(Table $table): Table
    {
        $query = Procurement::query()
            ->whereIn('status', ['APPROVED', 'COMPLETED', 'REJECTED'])
            ->latest('updated_at');

        return $table
            ->query($query)
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'OPEX' => 'info',
                        'CAPEX' => 'warning',
                        'CASH_ADVANCE' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->reason)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'APPROVED' => 'success',
                        'COMPLETED' => 'info',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->rejection_reason)
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('payment_proof')
                    ->label('Payment Proof')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rejected_at')
                    ->label('Rejected At')
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_proof_uploaded_at')
                    ->label('Proof Uploaded')
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'APPROVED' => 'Approved',
                        'COMPLETED' => 'Completed',
                        'REJECTED' => 'Rejected',
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'OPEX' => 'OPEX',
                        'CAPEX' => 'CAPEX',
                        'CASH_ADVANCE' => 'Cash Advance',
                    ])
                    ->label('Type'),

                Tables\Filters\TernaryFilter::make('payment_proof')
                    ->label('Payment Proof')
                    ->placeholder('All')
                    ->trueLabel('Uploaded')
                    ->falseLabel('Not Uploaded')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('payment_proof'),
                        false: fn ($query) => $query->whereNull('payment_proof'),
                    ),
            ])
            ->recordUrl(fn (Procurement $record): string =>
                route('filament.admin.resources.procurements.view', ['record' => $record->id])
            )
            ->heading('Procurement History - Approved & Rejected')
            ->description('Daftar procurement yang sudah di-approve, completed, atau di-reject. Klik row untuk melihat detail dan upload bukti pembayaran.')
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyState(view('filament.empty-states.finance-history-empty'))
            ->poll('30s')
            ->paginated([5, 10, 25, 50]);
    }
}
