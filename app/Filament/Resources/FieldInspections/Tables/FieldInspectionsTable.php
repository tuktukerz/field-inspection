<?php

namespace App\Filament\Resources\FieldInspections\Tables;

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
            ->recordActions([
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
