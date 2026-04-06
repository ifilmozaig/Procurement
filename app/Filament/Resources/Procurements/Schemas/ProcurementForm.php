<?php

namespace App\Filament\Resources\Procurements\Schemas;

use App\Models\Company;
use App\Models\ExpenseCategory;
use App\Models\ExpenseMasterItem;
use App\Models\Vendor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class ProcurementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Tipe Procurement')
                    ->options([
                        'OPEX'         => 'OPEX',
                        'CAPEX'        => 'CAPEX',
                        'CASH_ADVANCE' => 'CASH ADVANCE',
                    ])
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('reason')
                    ->label('Alasan Procurement')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Repeater::make('items')
                    ->relationship()
                    ->label('Item Procurement')
                    ->schema([

                        Select::make('_category_id')
                            ->label('Kategori Beban')
                            ->options(fn () => ExpenseCategory::active()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->placeholder('-- Pilih Kategori --')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('expense_master_item_id', null);
                                $set('item_name', null);
                                $set('unit', null);
                                $set('estimated_price', null);
                                $set('vendor_id', null);
                                $set('payment_method', null);
                                $set('bank_account_id', null);
                                $set('specification', null);
                            })
                            ->dehydrated(false)
                            ->columnSpan(2),

                        Select::make('expense_master_item_id')
                            ->label('Item dari Master Data')
                            ->placeholder('-- Pilih Item atau Ketik Manual --')
                            ->options(function (callable $get) {
                                $categoryId = $get('_category_id');
                                if (!$categoryId) {
                                    return ExpenseMasterItem::with('category')->active()->get()
                                        ->filter(fn ($item) => $item->category !== null)
                                        ->mapWithKeys(fn ($item) => [
                                            $item->id => $item->category->name . ' → ' . $item->item_name
                                        ])->toArray();
                                }
                                return ExpenseMasterItem::where('expense_category_id', $categoryId)
                                    ->active()->orderBy('sort_order')->pluck('item_name', 'id')->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) return;
                                $masterItem = ExpenseMasterItem::with('category')->find($state);
                                if (!$masterItem) return;

                                $set('item_name', $masterItem->item_name);
                                $set('specification', $masterItem->specification ?? '');
                                $set('unit', $masterItem->unit ?? '');

                                // ✅ FIX: Format ke ribuan Indonesia (300.000) sebelum di-set ke field
                                $price = $masterItem->estimated_price > 0 ? $masterItem->estimated_price : null;
                                $set('estimated_price', $price ? number_format((float) $price, 0, ',', '.') : null);

                                $set('vendor_id', $masterItem->vendor_id ?? null);
                                $set('_category_id', $masterItem->expense_category_id);
                                $set('payment_method', null);
                                $set('bank_account_id', null);
                            })
                            ->helperText('Pilih dari master data, atau kosongkan untuk isi manual')
                            ->columnSpan(2),

                        // Multi-company via Select multiple
                        // Filament otomatis sync BelongsToMany saat pakai ->relationship() + ->multiple()
                        Select::make('companies')
                            ->label('Untuk Perusahaan')
                            ->relationship(
                                name: 'companies',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query
                                    ->where('is_active', true)
                                    ->orderBy('name'),
                            )
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('-- Pilih Perusahaan --')
                            ->helperText('Bisa pilih lebih dari 1 perusahaan')
                            ->columnSpan(2),

                        TextInput::make('item_name')
                            ->label('Nama Item')
                            ->required()
                            ->placeholder('Nama item (otomatis dari master data)')
                            ->columnSpan(2),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('pcs, buah, kg, dll')
                            ->columnSpan(1),

                        TextInput::make('specification')
                            ->label('Spesifikasi')
                            ->columnSpan(3),

                        TextInput::make('quantity')
                            ->label('Qty')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->columnSpan(1),

                        // estimated_price: tampil format titik ribuan, simpan angka murni
                        TextInput::make('estimated_price')
                            ->label('Harga Estimasi (Rp)')
                            ->prefix('Rp')
                            ->required()
                            ->maxValue(9_999_999_999)
                            ->validationMessages(['max' => 'Estimated price maksimal adalah Rp 9.999.999.999'])
                            ->columnSpan(2)
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : '')
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', $state ?? ''))
                            ->rules(['nullable', 'regex:/^[\d.]+$/'])
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

                        Select::make('vendor_id')
                            ->label('Vendor')
                            ->options(function () {
                                return Vendor::where('is_active', true)->orderBy('name')->get()
                                    ->mapWithKeys(fn ($v) => [
                                        $v->id => $v->name
                                            . ($v->city ? " ({$v->city})" : '')
                                            . ($v->business_type ? " — {$v->business_type}" : ''),
                                    ])->toArray();
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $v = Vendor::find($value);
                                if (!$v) return $value;
                                return $v->name
                                    . ($v->city ? " ({$v->city})" : '')
                                    . ($v->business_type ? " — {$v->business_type}" : '');
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder('-- Pilih Vendor (opsional) --')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('payment_method', null);
                                $set('bank_account_id', null);
                            })
                            ->columnSpan(3),

                        Placeholder::make('_vendor_info')
                            ->label('')
                            ->content(function (callable $get) {
                                $vendorId = $get('vendor_id');
                                if (!$vendorId) return '';
                                $vendor = Vendor::with('bankAccounts')->find($vendorId);
                                if (!$vendor) return '';
                                $lines = [];
                                if ($vendor->pic_name) $lines[] = "👤 PIC: {$vendor->pic_name}";
                                if ($vendor->phone)    $lines[] = "📞 {$vendor->phone}";
                                if ($vendor->email)    $lines[] = "✉️ {$vendor->email}";
                                if ($vendor->address)  $lines[] = "📍 {$vendor->address}";
                                return implode('   •   ', $lines);
                            })
                            ->columnSpan(3)
                            ->hidden(fn (callable $get) => !$get('vendor_id')),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options(function (callable $get) {
                                $vendorId = $get('vendor_id');
                                if (!$vendorId) return [];
                                $vendor = Vendor::find($vendorId);
                                if (!$vendor || empty($vendor->payment_methods)) return [];
                                $labels = [
                                    'transfer' => '🏦 Transfer Bank',
                                    'cash'     => '💵 Tunai (Cash)',
                                    'cek'      => '📄 Cek',
                                    'giro'     => '📋 Giro',
                                    'lainnya'  => '🔖 Lainnya',
                                ];
                                return collect($vendor->payment_methods)
                                    ->mapWithKeys(fn ($method) => [
                                        $method => $labels[$method] ?? ucfirst($method)
                                    ])->toArray();
                            })
                            ->placeholder('-- Pilih Metode Pembayaran --')
                            ->searchable()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('bank_account_id', null))
                            ->columnSpan(3)
                            ->hidden(fn (callable $get) => !$get('vendor_id')),

                        Select::make('bank_account_id')
                            ->label('Rekening Bank Tujuan')
                            ->options(function (callable $get) {
                                $vendorId = $get('vendor_id');
                                if (!$vendorId) return [];
                                return \App\Models\VendorBankAccount::where('vendor_id', $vendorId)
                                    ->get()
                                    ->mapWithKeys(fn ($acc) => [
                                        $acc->id => $acc->bank_name
                                            . ' — ' . $acc->account_number
                                            . ' a/n ' . $acc->account_name
                                            . ($acc->is_primary ? ' ⭐' : ''),
                                    ])->toArray();
                            })
                            ->placeholder('-- Pilih Rekening --')
                            ->searchable()
                            ->nullable()
                            ->columnSpan(3)
                            ->hidden(fn (callable $get) =>
                                !$get('vendor_id') || $get('payment_method') !== 'transfer'
                            ),
                    ])
                    ->columns(6)
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Item')
                    ->collapsible()
                    ->columnSpanFull(),

                Repeater::make('attachments')
                    ->relationship()
                    ->label('Upload Quotation/Proposal')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('File')
                            ->disk('public')
                            ->directory('procurement-attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120)
                            ->required()
                            ->columnSpanFull()
                            ->imagePreviewHeight('250')
                            ->previewable()
                            ->downloadable()
                            ->openable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $fileName  = is_string($state) ? basename($state) : (is_object($state) ? $state->getClientOriginalName() : '');
                                    $extension = is_string($state) ? pathinfo($state, PATHINFO_EXTENSION) : (is_object($state) ? $state->getClientOriginalExtension() : '');
                                    $set('file_name', $fileName);
                                    $set('file_type', strtolower($extension));
                                }
                            }),
                        Hidden::make('file_name'),
                        Hidden::make('file_type'),
                    ])
                    ->columns(1)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah File')
                    ->collapsible()
                    ->columnSpanFull(),

                Hidden::make('user_id')->default(auth()->id()),
                Hidden::make('status')->default('DRAFT'),
            ]);
    }
}