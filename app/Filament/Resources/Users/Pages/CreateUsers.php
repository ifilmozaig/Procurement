<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateUsers extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected string $view = 'Admin.create-users';
    protected function getHeaderActions(): array
        {
            return [
                Actions\Action::make('back')
                    ->label('Back to Dashboard')
                    ->icon('heroicon-o-arrow-left')
                    ->url(UserResource::getUrl('index'))
                    ->color('gray'),
            ];
        }
    
    protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }
}
