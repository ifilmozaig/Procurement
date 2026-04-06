<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin'     => 'Super Admin',
                        'finance'         => 'Finance',
                        'finance_manager' => 'Finance Manager',
                        'hrga'            => 'HRGA',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable(),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText('Kosongkan jika tidak ingin mengubah password')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin'     => 'danger',
                        'finance_manager' => 'warning',
                        'finance'         => 'success',
                        'hrga'            => 'info',
                        default           => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin'     => 'Super Admin',
                        'finance'         => 'Finance',
                        'finance_manager' => 'Finance Manager',
                        'hrga'            => 'HRGA',
                        default           => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit'   => Pages\EditUsers::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        if ($record->id === auth()->id()) {
            return false;
        }

        if ($record->role === 'super_admin') {
            return false;
        }

        return auth()->user()->role === 'super_admin';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users Management';
    }

    public static function getModelLabel(): string
    {
        return 'User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Users';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}