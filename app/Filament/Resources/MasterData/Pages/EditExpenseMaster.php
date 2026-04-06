<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\ExpenseMasterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditExpenseMaster extends EditRecord
{
    protected static string $resource = ExpenseMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Daftar')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(ExpenseMasterResource::getUrl('index')),

            DeleteAction::make()->label('Hapus Kategori'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}