<?php

namespace App\Filament\Resources\Procurements\Pages;

use App\Filament\Resources\Procurements\ProcurementResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateProcurement extends CreateRecord
{
    protected static string $resource = ProcurementResource::class;
    protected string $view = 'admin.create-procurement';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status']  = $data['status'] ?? 'DRAFT';

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Dashboard')
                ->icon('heroicon-o-arrow-left')
                ->url(ProcurementResource::getUrl('index')) 
                ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}