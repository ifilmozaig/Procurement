<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\VendorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;
     public function getView(): string
    {
        return 'Admin.list-vendors'; 
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Vendor')
                ->visible(fn () => auth()->user()->hasRole(['hrga', 'finance', 'super_admin'])),
        ];
    }
}