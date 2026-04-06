<?php

namespace App\Filament\Resources\Procurements\Pages;

use App\Filament\Resources\Procurements\ProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProcurement extends EditRecord
{
    protected static string $resource = ProcurementResource::class;
    protected string $view = 'Admin.edit-procurement';
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Dashboard')
                ->icon('heroicon-o-arrow-left')
                ->url(ProcurementResource::getUrl('index'))
                ->color('gray'),

            Actions\ViewAction::make()
                ->label('View'),

            Actions\Action::make('save_and_submit')
                ->label('Save & Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Save & Submit Procurement')
                ->modalDescription('Are you sure you want to save and submit this procurement? It will be sent to Finance for review.')
                ->visible(fn ($record) => $record->status === 'DRAFT')
                ->action(function () {
                    $this->save();

                    $this->record->update([
                        'status' => 'PENDING',
                        'submitted_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Procurement Submitted')
                        ->body("Procurement {$this->record->procurement_number} has been saved and submitted for review.")
                        ->send();

                    return redirect()->route('filament.admin.resources.procurements.view', ['record' => $this->record]);
                }),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->visible(fn ($record) => $record->status === 'DRAFT'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Procurement updated successfully';
    }
}
