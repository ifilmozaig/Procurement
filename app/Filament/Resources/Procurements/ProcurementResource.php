<?php

namespace App\Filament\Resources\Procurements;

use App\Filament\Resources\Procurements\Pages\CreateProcurement;
use App\Filament\Resources\Procurements\Pages\EditProcurement;
use App\Filament\Resources\Procurements\Pages\ListProcurements;
use App\Filament\Resources\Procurements\Pages\ViewProcurement;
use App\Filament\Resources\Procurements\Schemas\ProcurementForm;
use App\Filament\Resources\Procurements\Schemas\ProcurementInfolist;
use App\Filament\Resources\Procurements\Tables\ProcurementsTable;
use App\Models\Procurement;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProcurementResource extends Resource
{
    protected static ?string $model = Procurement::class;
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'HRGA';
    }

    public static function getModelLabel(): string
    {
        return 'HRGA';
    }

    public static function getPluralModelLabel(): string
    {
        return 'HRGA';
    }

    protected static ?string $recordTitleAttribute = 'procurement_number';
    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasRole(['finance', 'finance_manager']);
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) return true;
        if ($user->hasRole(['hrga', 'finance', 'finance_manager'])) return true;
        return $record->user_id === $user->id;
    }

    public static function canCreate(): bool
    {
        return auth()->check();
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) return true;
        if ($record->user_id === $user->id && $record->status === 'DRAFT') return true;
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) return true;
        if ($record->user_id === $user->id &&
            in_array($record->status, ['DRAFT', 'REJECTED', 'COMPLETED'])) return true;
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ProcurementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcurementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = ProcurementsTable::configure($table);

        return $table
            ->recordUrl(fn (Procurement $record): string =>
                static::getUrl('view', ['record' => $record])
            )
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProcurements::route('/'),
            'create' => CreateProcurement::route('/create'),
            'view'   => ViewProcurement::route('/{record}'),
            'edit'   => EditProcurement::route('/{record}/edit'),
        ];
    }
}