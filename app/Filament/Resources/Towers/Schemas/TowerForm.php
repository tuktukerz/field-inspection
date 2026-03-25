<?php

namespace App\Filament\Resources\Towers\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\District;
use App\Models\SubDistrict;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class TowerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar Tower')
                    ->schema([
                        TextInput::make('tower_id')
                            ->label('ID Tower')
                            ->placeholder('Otomatis')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),

                        TextInput::make('location_name')
                            ->label('Nama Lokasi')
                            ->required(),

                        Textarea::make('location_detail')
                            ->label('Detail Lokasi')
                            ->columnSpanFull()
                            ->required(),

                        Select::make('kecamatan')
                            ->label('Kecamatan')
                            ->options(District::all()->pluck('name', 'name'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('kelurahan', null))
                            ->required(),

                        Select::make('kelurahan')
                            ->label('Kelurahan')
                            ->options(fn (Get $get): array => 
                                SubDistrict::whereHas('district', fn ($query) => $query->where('name', $get('kecamatan')))
                                    ->pluck('name', 'name')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Section::make('Lokasi & Koordinat')
                    ->schema([
                        Radio::make('location_mode')
                            ->label('Metode Input Lokasi')
                            ->options([
                                'gps' => 'Ambil dari GPS',
                                'manual' => 'Input Manual',
                            ])
                            ->default('manual')
                            ->afterStateUpdated(function (Set $set) {
                                $set('latitude', null);
                                $set('longitude', null);
                            })
                            ->live(),

                        Placeholder::make('gps_button')
                            ->visible(fn (Get $get) => $get('location_mode') === 'gps')
                            ->content(new HtmlString(<<<HTML
                                <button
                                    type="button"
                                    style="padding:10px 16px;background:#3b82f6;color:white;border-radius:8px;width:100%;"
                                    onclick="
                                        if (navigator.geolocation) {
                                            navigator.geolocation.getCurrentPosition(
                                                (position) => {
                                                    const lw = window.Livewire.find(
                                                        this.closest('[wire\\\\:id]').getAttribute('wire:id')
                                                    );

                                                    lw.set('data.latitude', String(position.coords.latitude));
                                                    lw.set('data.longitude', String(position.coords.longitude));
                                                },
                                                (error) => {
                                                    alert('Gagal ambil lokasi: ' + error.message);
                                                }
                                            );
                                        } else {
                                            alert('Browser tidak support GPS');
                                        }
                                    "
                                >
                                    Ambil Lokasi dari GPS 📍
                                </button>
                            HTML))
                            ->columnSpanFull(),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->placeholder('-6.200000')
                            ->readOnly(fn (Get $get) => $get('location_mode') === 'gps')
                            ->required(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->placeholder('106.816666')
                            ->readOnly(fn (Get $get) => $get('location_mode') === 'gps')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
