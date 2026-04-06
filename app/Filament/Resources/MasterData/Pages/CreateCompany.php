<?php

namespace App\Filament\Resources\MasterData\Pages;

use App\Filament\Resources\MasterData\CompanyResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back To List')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(CompanyResource::getUrl('index')),
        ];
    }
}