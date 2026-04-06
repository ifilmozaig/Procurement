<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\VendorResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back To List')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(VendorResource::getUrl('index')),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Vendor berhasil ditambahkan';
    }
}