<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\CompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;
    public function getView(): string
    {
        return 'Admin.list-companies'; 
    }
   
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Perusahaan')
                ->visible(fn () => auth()->user()->hasRole(['hrga', 'finance', 'super_admin'])), 
        ];
    }

}