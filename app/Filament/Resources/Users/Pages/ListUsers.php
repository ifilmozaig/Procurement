<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected string $view = 'admin.list-users';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah User'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-m-pencil-square'),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-m-trash')
                ->visible(fn (User $record): bool =>
                    $record->role !== 'super_admin' &&
                    $record->id !== auth()->id()
                )
                ->before(function (Actions\DeleteAction $action, User $record) {
                    if ($record->id === auth()->id()) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus akun sendiri')
                            ->body('Anda tidak dapat menghapus akun yang sedang Anda gunakan.')
                            ->send();
                        $action->cancel();
                    }

                    if ($record->role === 'super_admin') {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus Super Admin')
                            ->body('Akun dengan role Super Admin tidak dapat dihapus.')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Actions\DeleteBulkAction::make()
                ->label('Hapus Terpilih')
                ->before(function (Actions\DeleteBulkAction $action) {
                    $records = $this->getSelectedTableRecords();

                    $hasProtected = $records->contains(fn (User $record) =>
                        $record->role === 'super_admin' ||
                        $record->id === auth()->id()
                    );

                    if ($hasProtected) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus')
                            ->body('Beberapa akun yang dipilih adalah Super Admin atau akun Anda sendiri dan tidak dapat dihapus.')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
