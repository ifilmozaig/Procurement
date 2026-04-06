<?php

namespace App\Filament\Resources\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UsersTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
        {
            return $table
                ->query(User::query())
                ->defaultSort('created_at', 'desc')
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
                    
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Terdaftar')
                        ->dateTime('d M Y, H:i')
                        ->sortable(),
                ])
                ->paginated([10, 25, 50]);
        }
}
