<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUsers extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected string $view = 'admin.edit-users';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['password'] = '';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Dashboard')
                ->icon('heroicon-o-arrow-left')
                ->url(UserResource::getUrl('index'))
                ->color('gray'),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->before(function () {
                    if ($this->record->id === auth()->id()) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus akun sendiri')
                            ->body('Anda tidak dapat menghapus akun yang sedang Anda gunakan.')
                            ->send();

                        $this->halt();
                    }

                    if ($this->record->role === 'super_admin') {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus Super Admin')
                            ->body('Akun dengan role Super Admin tidak dapat dihapus.')
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
