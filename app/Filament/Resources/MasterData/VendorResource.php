<?php

namespace App\Filament\Resources\MasterData;

use App\Models\Vendor;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    protected static ?string $navigationLabel = 'Data Vendor';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 2;                        

    private static array $viewRoles = ['hrga', 'finance', 'super_admin'];
    private static array $editRoles = ['hrga', 'finance', 'super_admin'];

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole(self::$viewRoles);
    }

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                Section::make('Informasi Vendor')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Vendor / Perusahaan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('contoh: Toko Maju Jaya')
                            ->columnSpan(2),

                        Forms\Components\Select::make('business_type')
                            ->label('Bentuk Usaha')
                            ->options([
                                'PT'         => 'PT (Perseroan Terbatas)',
                                'CV'         => 'CV (Commanditaire Vennootschap)',
                                'UD'         => 'UD (Usaha Dagang)',
                                'Firma'      => 'Firma',
                                'Koperasi'   => 'Koperasi',
                                'Perorangan' => 'Perorangan',
                                'Lainnya'    => 'Lainnya',
                            ])
                            ->placeholder('Pilih bentuk usaha')
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('pic_name')
                            ->label('Nama PIC / Kontak')
                            ->maxLength(255)
                            ->placeholder('contoh: Budi Santoso')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone')
                            ->label('No HP / Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('contoh: 0812-3456-7890')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('contoh: vendor@email.com')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(100)
                            ->placeholder('contoh: Jakarta')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Jl. Contoh No. 1, Kelurahan, Kecamatan')
                            ->columnSpan(4),
                    ]),

                Section::make('Metode Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\CheckboxList::make('payment_methods')
                            ->label('')
                            ->options([
                                'transfer' => 'Transfer Bank',
                                'cash'     => 'Tunai (Cash)',
                                'cek'      => 'Cek',
                                'giro'     => 'Giro',
                                'lainnya'  => 'Lainnya',
                            ])
                            ->columns(5),
                    ]),

                Section::make('Rekening Bank')
                    ->icon('heroicon-o-banknotes')
                    ->description('Bisa lebih dari satu rekening. Centang "Utama" untuk rekening default.')
                    ->schema([
                        Forms\Components\Repeater::make('bankAccounts')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('bank_name')
                                    ->label('Nama Bank')
                                    ->options([
                                        'BCA'     => 'BCA (Bank Central Asia)',
                                        'BRI'     => 'BRI (Bank Rakyat Indonesia)',
                                        'BNI'     => 'BNI (Bank Negara Indonesia)',
                                        'Mandiri' => 'Bank Mandiri',
                                        'BSI'     => 'BSI (Bank Syariah Indonesia)',
                                        'CIMB'    => 'CIMB Niaga',
                                        'Danamon' => 'Bank Danamon',
                                        'Permata' => 'Bank Permata',
                                        'BTN'     => 'BTN (Bank Tabungan Negara)',
                                        'Lainnya' => 'Bank Lainnya',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Pilih bank')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('account_number')
                                    ->label('Nomor Rekening')
                                    ->required()
                                    ->maxLength(30)
                                    ->placeholder('contoh: 1234567890')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('account_name')
                                    ->label('Atas Nama')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('contoh: Toko Maju Jaya')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_primary')
                                    ->label('Rekening Utama')
                                    ->default(false)
                                    ->inline(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->minItems(0)
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Rekening')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                !empty($state['bank_name']) && !empty($state['account_number'])
                                    ? "{$state['bank_name']} — {$state['account_number']}" . (!empty($state['account_name']) ? " a/n {$state['account_name']}" : '')
                                    : null
                            ),
                    ]),

                Section::make()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Vendor Aktif')
                            ->default(true),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Vendor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->business_type ?? ''),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No HP')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_accounts_count')
                    ->label('Rekening')
                    ->counts('bankAccounts')
                    ->badge()
                    ->color('info'),
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
                Tables\Filters\SelectFilter::make('business_type')
                    ->label('Bentuk Usaha')
                    ->options([
                        'PT' => 'PT', 'CV' => 'CV', 'UD' => 'UD',
                        'Perorangan' => 'Perorangan', 'Lainnya' => 'Lainnya',
                    ]),
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
                        ->label('Hapus Terpilih')
                        ->visible(fn () => auth()->user()->hasRole(self::$editRoles)),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->heading('Master Data Vendor')
            ->description('Kelola daftar vendor beserta rekening bank dan metode pembayaran.');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string { return 'Vendor'; }
    public static function getPluralModelLabel(): string { return 'Data Vendor'; }
}