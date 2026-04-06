<?php

namespace App\Filament\Resources\MasterData;

use App\Models\ExpenseCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseMasterResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;
    protected static ?string $navigationLabel = 'Data Beban';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';
    protected static ?int $navigationSort = 1;

    // Role yang bisa melihat menu Master Data (Data Beban)
    private static array $viewRoles = ['hrga', 'finance', 'super_admin'];

    // Role yang bisa edit/hapus
    private static array $editRoles = ['hrga', 'finance', 'super_admin'];

    // Role yang bisa akses halaman Realisasi (termasuk finance_manager)
    private static array $realisasiRoles = ['hrga', 'finance', 'finance_manager', 'super_admin'];

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    // Hanya tampilkan menu "Data Beban" untuk $viewRoles (finance_manager tidak termasuk)
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole(self::$viewRoles);
    }

    // Izinkan akses halaman jika punya salah satu role (viewRoles ATAU realisasiRoles)
    // Ini penting agar finance_manager bisa buka halaman /realisasi tanpa 403
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(
            array_unique(array_merge(self::$viewRoles, self::$realisasiRoles))
        );
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

    protected static function generateCode(string $name): string
    {
        $name = trim($name);
        if (empty($name)) return '';
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            return implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), $words));
        }
        return strtoupper(substr($words[0], 0, 3));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(255)
                ->placeholder('contoh: Beban Konsumsi')
                ->live(debounce: 500)
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    if (empty($get('code'))) {
                        $set('code', static::generateCode($state ?? ''));
                    }
                }),

            Forms\Components\TextInput::make('code')
                ->label('Kode')
                ->maxLength(20)
                ->placeholder('contoh: BK')
                ->helperText('Terisi otomatis dari nama kategori. Bisa dihapus/diubah manual.'),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            Forms\Components\Repeater::make('masterItems')
                ->relationship()
                ->label('Daftar Item')
                ->schema([
                    Forms\Components\TextInput::make('item_name')
                        ->label('Nama Item')
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('specification')
                        ->label('Spesifikasi')
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('unit')
                        ->label('Satuan')
                        ->placeholder('pcs / buah / kg / ruko / dll'),
                    Forms\Components\TextInput::make('estimated_price')
                        ->label('Harga Estimasi (Rp)')
                        ->prefix('Rp')
                        ->default(0)
                        ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : '')
                        ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', $state ?? ''))
                        ->extraInputAttributes([
                            'x-data'     => '{}',
                            'x-on:input' => "
                                let raw = \$event.target.value.replace(/\\./g, '').replace(/[^0-9]/g, '');
                                if (raw === '') { \$event.target.value = ''; return; }
                                let formatted = parseInt(raw, 10).toLocaleString('id-ID');
                                \$event.target.value = formatted;
                                \$event.target.dispatchEvent(new Event('change'));
                            ",
                            'inputmode' => 'numeric',
                        ]),
                    Forms\Components\TextInput::make('vendor')
                        ->label('Vendor Default'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->inline(false),
                ])
                ->columns(4)
                ->minItems(0)
                ->defaultItems(0)
                ->addActionLabel('+ Tambah Item')
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('No.')
                    ->sortable()
                    ->width(60),
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori Beban')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('jumlah_item')
                    ->label('Jumlah Item')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn($record) => $record->masterItems()->count()),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->visible(fn() => auth()->user()->hasRole(['hrga', 'finance', 'super_admin'])),
                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn() => auth()->user()->hasRole(['hrga', 'finance', 'super_admin'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->visible(fn() => auth()->user()->hasRole(['hrga', 'finance', 'super_admin'])),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->heading('Master Data Kategori Beban')
            ->description('Kelola kategori dan item beban yang tersedia untuk digunakan pada procurement.');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListExpenseMaster::route('/'),
            'create'    => Pages\CreateExpenseMaster::route('/create'),
            'edit'      => Pages\EditExpenseMaster::route('/{record}/edit'),
            'realisasi' => Pages\RealisasiPengadaan::route('/realisasi'),
        ];
    }

    public static function getModelLabel(): string { return 'Kategori Beban'; }
    public static function getPluralModelLabel(): string { return 'Data Beban'; }
}