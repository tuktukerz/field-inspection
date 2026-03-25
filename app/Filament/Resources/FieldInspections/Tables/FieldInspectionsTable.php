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

                // 🔹 KELURAHAN
                TextColumn::make('kelurahan')
                    ->label('Kelurahan')
                    ->searchable(),

                // 🔹 KECAMATAN
                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
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
                Action::make('copy_info')
                    ->label('Salin Info')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('info')
                    ->alpineClickHandler(fn ($record) => "
                        const copyText = atob('" . base64_encode(
                            "LOKASI\t: {$record->location_name}\n" .
                            "DETAIL\t: {$record->location_detail}\n" .
                            "KECAMATAN\t: {$record->kecamatan}\n" .
                            "KELURAHAN\t: {$record->kelurahan}\n" .
                            "LETAK TITIK\t: " . strtoupper($record->location_type) . "\n" .
                            "LATITUDE\t: {$record->latitude}\n" .
                            "LONGITUDE\t: {$record->longitude}\n" .
                            "GOOGLE MAPS\t: https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}"
                        ) . "');
                        
                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText(copyText).then(() => {
                                alert('Info berhasil disalin ke clipboard!');
                            }).catch(err => {
                                console.error('Clipboard error:', err);
                                alert('Gagal salin: ' + err);
                            });
                        } else {
                            const textArea = document.createElement('textarea');
                            textArea.value = copyText;
                            document.body.appendChild(textArea);
                            textArea.select();
                            try {
                                document.execCommand('copy');
                                alert('Info berhasil disalin ke clipboard (Fallback)!');
                            } catch (err) {
                                alert('Gagal salin total!');
                            }
                            document.body.removeChild(textArea);
                        }
                    "),
                Action::make('open_map')
                    ->label('Buka Peta')
                    ->icon('heroicon-o-map-pin')
                    ->color('success')
                    ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}")
                    ->openUrlInNewTab(),
                EditAction::make(),
                ViewAction::make(),
            ])
            ->recordUrl(fn ($record) => route('filament.admin.resources.field-inspections.view', $record))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
