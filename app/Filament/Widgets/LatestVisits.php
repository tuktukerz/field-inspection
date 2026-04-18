<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestVisits extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Daftar Inspeksi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Visit::query()->latest('inspection_date')->limit(5)
            )
            ->columns([
                TextColumn::make('tower.tower_id')
                    ->label('ID Menara')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inspection_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tower.location_name')
                    ->label('Lokasi')
                    ->description(fn ($record) => $record?->tower?->location_detail)
                    ->wrap(),
                TextColumn::make('tower.kecamatan')
                    ->label('Wilayah')
                    ->badge()
                    ->color('info')
                    ->description(fn ($record) => "Kel. {$record?->tower?->kelurahan}"),
                TextColumn::make('creator.name')
                    ->label('Pemeriksa'),
            ])
            ->paginated(false);
    }
}
