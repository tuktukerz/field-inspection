<?php

namespace App\Filament\Resources\Towers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TowersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tower_id')
                    ->label('ID Tower')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location_name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable(),

                TextColumn::make('kelurahan')
                    ->label('Kelurahan')
                    ->searchable(),

                TextColumn::make('visits_count')
                    ->label('Jumlah Kunjungan')
                    ->counts('visits'),
            ]);
    }
}
