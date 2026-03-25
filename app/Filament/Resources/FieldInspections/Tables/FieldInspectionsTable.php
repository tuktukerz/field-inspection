<?php

namespace App\Filament\Resources\FieldInspections\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class FieldInspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 🔹 NO
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                // 🔹 NOMOR DOKUMEN
                TextColumn::make('document_number')
                    ->label('Nomor Dokumen')
                    ->searchable()
                    ->sortable(),

                // 🔹 TANGGAL
                TextColumn::make('inspection_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                // 🔹 LOKASI
                TextColumn::make('tower.location_name')
                    ->label('Lokasi Menara')
                    ->description(fn($record) => $record->tower->location_detail)
                    ->searchable()
                    ->sortable(),

                // 🔹 KELURAHAN
                TextColumn::make('kelurahan')
                    ->label('Kelurahan')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                // 🔹 KECAMATAN
                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                // 🔹 DIBUAT OLEH
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('salin_data')
                    ->label('Salin Data')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('gray')
                    ->button()
                    ->alpineClickHandler(fn ($record) => "
                        const text = `ID MENARA : {$record->tower?->tower_id}\\nLOKASI MENARA : {$record->tower?->location_name}\\nDETAIL : {$record->tower?->location_detail}\\nKECAMATAN : {$record->kecamatan}\\nKELURAHAN : {$record->kelurahan}\\nLATITUDE : {$record->latitude}\\nLONGITUDE : {$record->longitude}\\nGOOGLE MAPS : https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}`;
                        window.navigator.clipboard.writeText(text);
                        new FilamentNotification()
                            .title('Info berhasil disalin!')
                            .success()
                            .send();
                    "),
                Action::make('open_maps')
                    ->label('Maps')
                    ->icon('heroicon-m-map-pin')
                    ->color('primary')
                    ->button()
                    ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}")
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->recordUrl(fn ($record) => route('filament.admin.resources.field-inspections.view', $record))
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
