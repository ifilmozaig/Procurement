<?php
namespace App\Filament\Resources\Widgets;

use App\Filament\Resources\Procurements\Support\ProcurementStatus;
use App\Models\Procurement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardFinanceStaffTable extends BaseWidget
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
            ->whereIn('status', ['PENDING', 'APPROVED'])
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
                    ->color(fn (string $state): string => ProcurementStatus::color($state))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PENDING'  => 'Need Review',
                        'APPROVED' => 'Need Payment Proof',
                        default    => ProcurementStatus::label($state),
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('action_required')
                    ->label('Action Required')
                    ->badge()
                    ->color(fn (Procurement $record): string =>
                        $record->status === 'PENDING' ? 'warning' : 'info'
                    )
                    ->formatStateUsing(fn (Procurement $record): string =>
                        $record->status === 'PENDING'
                            ? ($record->type === 'OPEX' ? 'Review & Approve' : 'Review & Forward')
                            : 'Upload Payment Proof'
                    ),

                Tables\Columns\IconColumn::make('payment_proof')
                    ->label('Payment Proof')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y H:i')
                    ->searchable()
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
                        'PENDING' => 'Need Review',
                        'APPROVED' => 'Need Payment Proof',
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

                Tables\Filters\Filter::make('submitted_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('submitted_from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('submitted_until')->label('Until'),
                        \App\Filament\Resources\Widgets\ProcurementGroupedTableWidget::class,
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['submitted_from'], fn ($q, $date) => $q->whereDate('submitted_at', '>=', $date))
                            ->when($data['submitted_until'], fn ($q, $date) => $q->whereDate('submitted_at', '<=', $date));
                    }),
            ])
            ->recordUrl(fn (Procurement $record): string =>
                route('filament.admin.resources.procurements.view', ['record' => $record->id])
            )
            ->heading('Finance Work Queue')
            ->description('Daftar procurement yang perlu ditangani: Review procurement yang PENDING atau Upload payment proof untuk yang sudah APPROVED. Klik row untuk aksi.')
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyState(view('filament.empty-states.finance-queue-empty'))
            ->poll('30s')
            ->paginated([5, 10, 25, 50]);
    }
}
