<?php

namespace App\Filament\Resources\Visits\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\RelationManagers\RelationManager;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),

                TextColumn::make('tower.tower_id')
                    ->label('Tower ID')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn ($livewire) => $livewire instanceof RelationManager),

                TextColumn::make('tower.location_name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tower.kecamatan')
                    ->label('Kecamatan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tower.location_detail')
                    ->label('Detail Lokasi')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('inspection_date')
                    ->label('Tanggal Inspeksi')
                    ->date()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Visited By'),
            ])
            ->filters([
                SelectFilter::make('tower_id')
                    ->label('Filter by Tower')
                    ->relationship('tower', 'tower_id')
                    ->searchable()
                    ->preload()
                    ->hidden(fn ($livewire) => $livewire instanceof RelationManager),

                SelectFilter::make('created_by')
                    ->label('Filter by Visitor')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
