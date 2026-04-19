<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Carbon\Carbon;
use Filament\Support\Enums\FontWeight;
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
                Visit::query()
                    ->with(['tower', 'creator'])
                    ->latest('inspection_date')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex()
                    ->weight(FontWeight::Bold)
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('tower.tower_id')
                    ->label('ID Menara')
                    ->icon('heroicon-o-signal')
                    ->iconColor('primary')
                    ->weight(FontWeight::SemiBold)
                    ->copyable()
                    ->copyMessage('ID Menara disalin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('inspection_date')
                    ->label('Tanggal Inspeksi')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('primary')
                    ->date('d M Y')
                    ->description(fn ($record) =>
                        $record?->inspection_date
                            ? Carbon::parse($record->inspection_date)->locale('id')->diffForHumans()
                            : null
                    )
                    ->sortable(),

                TextColumn::make('tower.location_name')
                    ->label('Lokasi Menara')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->weight(FontWeight::Medium)
                    ->description(fn ($record) => $record?->tower?->location_detail)
                    ->wrap(),

                TextColumn::make('tower.kecamatan')
                    ->label('Wilayah')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-building-office-2')
                    ->description(fn ($record) => $record?->tower?->kelurahan
                        ? "Kel. {$record->tower->kelurahan}"
                        : null
                    ),

                TextColumn::make('creator.name')
                    ->label('Pemeriksa')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('success')
                    ->weight(FontWeight::Medium),
            ])
            ->striped()
            ->paginated(false);
    }
}
