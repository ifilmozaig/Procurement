<?php

namespace App\Filament\Resources\MasterData;

use App\Models\Company;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationLabel  = 'Data Perusahaan';
    protected static ?string $modelLabel        = 'Perusahaan';
    protected static ?string $pluralModelLabel  = 'Data Perusahaan';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 3;

    private static array $viewRoles  = ['hrga', 'finance', 'super_admin'];
    private static array $editRoles  = ['hrga', 'finance', 'super_admin']; 
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole(self::$viewRoles);
    }

    // ── PERMISSION METHODS ────────────────────────────────────────────────
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(self::$viewRoles);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(self::$editRoles);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->hasRole(self::$editRoles);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->hasRole(self::$editRoles);
    }
    // ─────────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Perusahaan')
                ->icon('heroicon-o-building-office')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Perusahaan')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('contoh: PT Konnco Studio'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Perusahaan Aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->visible(fn () => auth()->user()->hasRole(self::$editRoles)),

                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn () => auth()->user()->hasRole(self::$editRoles)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole(self::$editRoles)),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit'   => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}