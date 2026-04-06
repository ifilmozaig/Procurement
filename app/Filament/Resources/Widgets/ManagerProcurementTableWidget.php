<?php
namespace App\Filament\Resources\Widgets;

use App\Models\Procurement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ManagerProcurementTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = null;
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['finance_manager', 'super_admin']);
    }

    protected function paginateTableQuery(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->getTableRecordsPerPage());
    }

    public function table(Table $table): Table
    {
        $query = Procurement::query()
            ->whereIn('status', ['PROCESSING', 'APPROVED', 'COMPLETED', 'REJECTED'])
            ->whereIn('type', ['CAPEX', 'CASH_ADVANCE'])
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
                        'CAPEX' => 'warning',
                        'CASH_ADVANCE' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->reason)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PROCESSING' => 'warning',
                        'APPROVED' => 'info',
                        'COMPLETED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->rejection_reason)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('payment_proof')
                    ->label('Payment Proof')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('rejected_at')
                    ->label('Rejected At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('payment_proof_uploaded_at')
                    ->label('Payment Proof Uploaded')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PROCESSING' => 'Processing (Pending)',
                        'APPROVED' => 'Approved',
                        'COMPLETED' => 'Completed',
                        'REJECTED' => 'Rejected',
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
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

                Tables\Filters\Filter::make('submitted_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('submitted_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('submitted_until')
                            ->label('Until'),
                        \App\Filament\Resources\Widgets\ProcurementGroupedTableWidget::class,
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['submitted_from'], fn ($q, $date) =>
                                $q->whereDate('submitted_at', '>=', $date))
                            ->when($data['submitted_until'], fn ($q, $date) =>
                                $q->whereDate('submitted_at', '<=', $date));
                    }),

                Tables\Filters\Filter::make('approved_this_month')
                    ->label('Approved This Month')
                    ->query(fn ($query) => $query->whereMonth('approved_at', now()->month))
                    ->toggle(),
            ])
            ->recordUrl(fn (Procurement $record): string =>
                route('filament.admin.resources.procurements.view', ['record' => $record->id])
            )
            ->heading('CAPEX & Cash Advance History')
            ->description('Daftar procurement CAPEX dan Cash Advance yang perlu approval atau sudah diproses. Klik row untuk melihat detail lengkap.')
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyState(view('filament.empty-states.manager-empty'))
            ->poll('30s')
            ->striped()
            ->paginated([5, 10, 25, 50]);
    }
}
