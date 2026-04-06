<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\ExpenseMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseMaster extends ListRecords
{
    protected static string $resource = ExpenseMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kategori')
                ->visible(fn () => auth()->user()->hasRole(['hrga','finance', 'super_admin'])),
        ];
    }

    public function getView(): string
    {
        return 'Admin.list';
    }
}