<?php

namespace App\Filament\Resources\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountProfileWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'admin.account-profile-widget';

    protected int | string | array $columnSpan = 1;

    public ?array $data = [];

    public bool $showForm = false;

    public function mount(): void
    {
        $this->form->fill([
            'name'                  => auth()->user()->name,
            'email'                 => auth()->user()->email,
            'password'              => '',
            'password_confirmation' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique('users', 'email', ignorable: auth()->user()),

                TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->same('password')
                    ->validationMessages([
                        'same' => 'Konfirmasi password tidak cocok dengan password baru.',
                    ])
                    ->helperText('Ulangi password baru'),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if (filled($data['password'])) {
            User::where('id', $user->id)->update([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            auth()->loginUsingId($user->id, true);
        } else {
            User::where('id', $user->id)->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);
        }

        $this->showForm = false;

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->send();

        $this->redirect('/admin');
    }
}