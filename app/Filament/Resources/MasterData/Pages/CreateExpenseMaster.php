<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\ExpenseMasterResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseMaster extends CreateRecord
{
    protected static string $resource = ExpenseMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back To List')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(ExpenseMasterResource::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}