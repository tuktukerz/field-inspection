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
                    ->label('ID Menara')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn ($livewire) => $livewire instanceof RelationManager),

                TextColumn::make('tower.location_name')
                    ->label('Alamat Detail')
                    ->description(fn ($record) => $record?->tower?->location_detail)
                    ->searchable(['location_name', 'location_detail'])
                    ->wrap(),

                TextColumn::make('tower.kecamatan')
                    ->label('Wilayah')
                    ->description(fn ($record) => "Kel. {$record?->tower?->kelurahan}")
                    ->searchable(['kecamatan', 'kelurahan']),

                TextColumn::make('inspection_date')
                    ->label('Tanggal Inspeksi')
                    ->date()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Pemeriksa'),
            ])
            ->filters([
                SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->options(\App\Models\Tower::query()->distinct()->whereNotNull('kecamatan')->pluck('kecamatan', 'kecamatan'))
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }
                        return $query->whereHas('tower', fn ($q) => $q->where('kecamatan', $data['value']));
                    }),

                SelectFilter::make('tower_id')
                    ->label('ID Menara')
                    ->relationship('tower', 'tower_id')
                    ->searchable()
                    ->preload()
                    ->hidden(fn ($livewire) => $livewire instanceof RelationManager),

                SelectFilter::make('created_by')
                    ->label('Pemeriksa')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->striped()
            ->defaultSort('inspection_date', 'desc')
            ->paginationPageOptions([10, 25, 50, 100])
            ->emptyStateHeading('Belum ada riwayat visit')
            ->emptyStateDescription('Tambahkan riwayat visit pertama untuk mulai mencatat inspeksi menara.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
