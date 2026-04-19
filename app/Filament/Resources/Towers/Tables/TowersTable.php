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
                TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),

                TextColumn::make('tower_id')
                    ->label('ID Menara')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location_name')
                    ->label('Nama Lokasi')
                    ->description(fn($record) => $record->location_detail)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable(),

                TextColumn::make('kelurahan')
                    ->label('Kelurahan')
                    ->searchable(),

                TextColumn::make('visits_count')
                    ->label('Inspeksi')
                    ->counts('visits')
                    ->badge()
                    ->color('success'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 25, 50, 100])
            ->emptyStateHeading('Belum ada menara')
            ->emptyStateDescription('Tambahkan menara telekomunikasi pertama untuk mulai inspeksi.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    public static function actions(): array
    {
        return [
            \Filament\Actions\Action::make('salin_data')
                ->label('Salin Data')
                ->icon('heroicon-m-clipboard-document-list')
                ->color('gray')
                ->button()
                ->alpineClickHandler(fn ($record) => "
                    const text = `ID MENARA : {$record->tower_id}\\nLOKASI MENARA : {$record->location_name}\\nDETAIL : {$record->location_detail}\\nKECAMATAN : {$record->kecamatan}\\nKELURAHAN : {$record->kelurahan}\\nLATITUDE : {$record->latitude}\\nLONGITUDE : {$record->longitude}\\nGOOGLE MAPS : https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}`;
                    window.navigator.clipboard.writeText(text);
                    new FilamentNotification()
                        .title('Info berhasil disalin!')
                        .success()
                        .send();
                "),
            \Filament\Actions\Action::make('open_maps')
                ->label('Maps')
                ->icon('heroicon-m-map-pin')
                ->color('primary')
                ->button()
                ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}")
                ->openUrlInNewTab(),
            \Filament\Actions\ViewAction::make(),
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
