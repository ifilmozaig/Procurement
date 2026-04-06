<?php
namespace App\Filament\Resources\Procurements\Tables;

use App\Filament\Resources\Procurements\Support\ProcurementStatus;
use App\Models\Procurement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProcurementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(fn ($livewire) => $livewire->getTableRecords()->count() > 0
                ? 'List Requester'
                : 'Belum ada List Requester'
            )
            ->description(fn ($livewire) => $livewire->getTableRecords()->count() > 0
                ? 'Berikut adalah daftar procurement yang telah dibuat oleh requester. Klik baris untuk melihat detail.'
                : 'List procurement yang dibuat oleh requester akan tampil di sini setelah ada pengajuan.'
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('procurement_number')
                    ->label('Procurement Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => ProcurementStatus::color($state))
                    ->formatStateUsing(fn (string $state): string => ProcurementStatus::label($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('rejected_at')
                    ->label('Rejected At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('completed_at')
                    ->label('Completed At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('submitted_at')
                    ->label('Submitted At')
                    ->form([
                        DatePicker::make('submitted_from')->label('Submitted Dari')->native(false)->displayFormat('d M Y'),
                        DatePicker::make('submitted_until')->label('Submitted Sampai')->native(false)->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['submitted_from'], fn (Builder $q, $date) => $q->whereDate('submitted_at', '>=', $date))
                            ->when($data['submitted_until'], fn (Builder $q, $date) => $q->whereDate('submitted_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['submitted_from'] ?? null) $indicators[] = 'Submitted dari: ' . \Carbon\Carbon::parse($data['submitted_from'])->format('d M Y');
                        if ($data['submitted_until'] ?? null) $indicators[] = 'Submitted sampai: ' . \Carbon\Carbon::parse($data['submitted_until'])->format('d M Y');
                        return $indicators;
                    }),

                Filter::make('approved_at')
                    ->label('Approved At')
                    ->form([
                        DatePicker::make('approved_from')->label('Approved Dari')->native(false)->displayFormat('d M Y'),
                        DatePicker::make('approved_until')->label('Approved Sampai')->native(false)->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['approved_from'], fn (Builder $q, $date) => $q->whereDate('approved_at', '>=', $date))
                            ->when($data['approved_until'], fn (Builder $q, $date) => $q->whereDate('approved_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['approved_from'] ?? null) $indicators[] = 'Approved dari: ' . \Carbon\Carbon::parse($data['approved_from'])->format('d M Y');
                        if ($data['approved_until'] ?? null) $indicators[] = 'Approved sampai: ' . \Carbon\Carbon::parse($data['approved_until'])->format('d M Y');
                        return $indicators;
                    }),

                Filter::make('rejected_at')
                    ->label('Rejected At')
                    ->form([
                        DatePicker::make('rejected_from')->label('Rejected Dari')->native(false)->displayFormat('d M Y'),
                        DatePicker::make('rejected_until')->label('Rejected Sampai')->native(false)->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['rejected_from'], fn (Builder $q, $date) => $q->whereDate('rejected_at', '>=', $date))
                            ->when($data['rejected_until'], fn (Builder $q, $date) => $q->whereDate('rejected_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['rejected_from'] ?? null) $indicators[] = 'Rejected dari: ' . \Carbon\Carbon::parse($data['rejected_from'])->format('d M Y');
                        if ($data['rejected_until'] ?? null) $indicators[] = 'Rejected sampai: ' . \Carbon\Carbon::parse($data['rejected_until'])->format('d M Y');
                        return $indicators;
                    }),

                Filter::make('completed_at')
                    ->label('Completed At')
                    ->form([
                        DatePicker::make('completed_from')->label('Completed Dari')->native(false)->displayFormat('d M Y'),
                        DatePicker::make('completed_until')->label('Completed Sampai')->native(false)->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['completed_from'], fn (Builder $q, $date) => $q->whereDate('completed_at', '>=', $date))
                            ->when($data['completed_until'], fn (Builder $q, $date) => $q->whereDate('completed_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['completed_from'] ?? null) $indicators[] = 'Completed dari: ' . \Carbon\Carbon::parse($data['completed_from'])->format('d M Y');
                        if ($data['completed_until'] ?? null) $indicators[] = 'Completed sampai: ' . \Carbon\Carbon::parse($data['completed_until'])->format('d M Y');
                        return $indicators;
                    }),
            ])
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyState(view('filament.empty-states.procurement-empty'))
            ->headerActions([
                CreateAction::make()
                    ->label('Buat procurement'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View'),

                EditAction::make()
                    ->label('Edit')
                    ->visible(fn (Procurement $record): bool =>
                        auth()->user()->hasRole('super_admin') ||
                        ($record->user_id === auth()->id() && $record->status === 'DRAFT')
                    ),

                DeleteAction::make()
                    ->label('Delete')
                    ->visible(fn (Procurement $record): bool =>
                        auth()->user()->hasRole('super_admin') ||
                        ($record->user_id === auth()->id() &&
                         in_array($record->status, ['DRAFT', 'REJECTED', 'COMPLETED']))
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Procurement')
                    ->modalDescription(fn (Procurement $record): string =>
                        auth()->user()->hasRole('super_admin')
                            ? 'Anda yakin ingin menghapus procurement ini? Tindakan ini tidak dapat dibatalkan.'
                            : 'Hanya procurement yang anda buat dengan status DRAFT, REJECTED dan COMPLETED yang bisa anda hapus.'
                    )
                    ->modalSubmitActionLabel('Hapus')
                    ->successNotificationTitle('Procurement Dihapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->successNotification(null)
                        ->action(function (Collection $records) {
                            if (auth()->user()->hasRole('super_admin')) {
                                $records->each->delete();
                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('Procurement Dihapus')
                                    ->duration(5000)
                                    ->send();
                                return;
                            }

                            $notOwnedRecords = $records->filter(function ($record) {
                                return $record->user_id !== auth()->id();
                            });

                            $invalidStatusRecords = $records->filter(function ($record) {
                                return $record->user_id === auth()->id() &&
                                    !in_array($record->status, ['DRAFT', 'REJECTED', 'COMPLETED']);
                            });

                            if ($notOwnedRecords->count() > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Tidak Dapat Menghapus')
                                    ->body('Hanya procurement anda dengan status DRAFT, REJECTED dan COMPLETED yang bisa anda hapus.')
                                    ->duration(5000)
                                    ->send();
                                return;
                            }

                            if ($invalidStatusRecords->count() > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Tidak Dapat Menghapus')
                                    ->body('Hanya procurement yang anda buat dengan status DRAFT, REJECTED dan COMPLETED yang bisa anda hapus.')
                                    ->duration(5000)
                                    ->send();
                                return;
                            }

                            $records->each->delete();
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Procurement Dihapus')
                                ->duration(5000)
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Procurement yang Dipilih')
                        ->modalDescription(fn (): string =>
                            auth()->user()->hasRole('super_admin')
                                ? 'Anda yakin ingin menghapus procurement yang dipilih? Tindakan ini tidak dapat dibatalkan.'
                                : 'Hanya procurement yang anda buat dengan status DRAFT, REJECTED dan COMPLETED yang bisa anda hapus.'
                        )
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Kembali'),
                ]),
            ])
            ->selectCurrentPageOnly();
    }
}