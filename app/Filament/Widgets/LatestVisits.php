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
                    ->label('Tower ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inspection_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tower.location_name')
                    ->label('Lokasi')
                    ->limit(30),
                TextColumn::make('tower.kecamatan')
                    ->label('Kecamatan')
                    ->sortable(),
                TextColumn::make('tower.location_detail')
                    ->label('Detail Lokasi')
                    ->limit(50),
                TextColumn::make('creator.name')
                    ->label('Pemeriksa'),
            ])
            ->paginated(false);
    }
}
