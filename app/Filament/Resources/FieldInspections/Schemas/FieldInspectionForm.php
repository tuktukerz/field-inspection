<?php

namespace App\Filament\Resources\FieldInspections\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;


use Illuminate\Support\HtmlString;



class FieldInspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =========================
                // SECTION 1 — DATA LAPANGAN
                // =========================
                Section::make('Data Lapangan')
                    ->schema([
                        DatePicker::make('inspection_date')
                            ->required(),

                        TextInput::make('location_name')
                            ->label('Lokasi Menara')
                            ->required(),

                        Textarea::make('location_detail')
                            ->label('Lokasi Detail')
                            ->columnSpanFull(),

                        TextInput::make('kelurahan'),
                        TextInput::make('kecamatan'),

                        Section::make('Lokasi')
                            ->schema([

                                // 🔹 MODE PILIHAN
                                Radio::make('location_mode')
                                    ->label('Metode Input Lokasi')
                                    ->options([
                                        'gps' => 'Ambil dari GPS',
                                        'manual' => 'Input Manual',
                                    ])
                                    ->default('manual')
                                    ->live(),

                                // 🔹 BUTTON GPS
                                Placeholder::make('gps_button')
                                    ->visible(fn ($get) => $get('location_mode') === 'gps')
                                    ->content(new HtmlString('
                                        <button
                                            type="button"
                                            style="padding:10px 16px;background:#3b82f6;color:white;border-radius:8px;width:100%;"
                                            onclick="
                                                if (navigator.geolocation) {
                                                    navigator.geolocation.getCurrentPosition(
                                                        (position) => {
                                                            const lw = window.Livewire.find(
                                                                document.querySelector(\'[wire\\:id]\').getAttribute(\'wire:id\')
                                                            );

                                                            lw.set(\'data.latitude\', position.coords.latitude);
                                                            lw.set(\'data.longitude\', position.coords.longitude);
                                                        },
                                                        (error) => {
                                                            alert(\'Gagal ambil lokasi: \' + error.message);
                                                        }
                                                    );
                                                } else {
                                                    alert(\'Browser tidak support GPS\');
                                                }
                                            "
                                        >
                                            Ambil Lokasi dari GPS 📍
                                        </button>
                                    '))
                                    ->columnSpanFull(),

                                // 🔹 LATITUDE
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('-6.200000')
                                    ->readOnly(fn ($get) => $get('location_mode') === 'gps')
                                    ->helperText(fn ($get) =>
                                    $get('location_mode') === 'gps'
                                        ? 'Akan terisi otomatis dari GPS'
                                        : 'Isi manual koordinat latitude'
                                    )
                                    ->required(),

                                // 🔹 LONGITUDE
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('106.816666')
                                    ->readOnly(fn ($get) => $get('location_mode') === 'gps')
                                    ->helperText(fn ($get) =>
                                    $get('location_mode') === 'gps'
                                        ? 'Akan terisi otomatis dari GPS'
                                        : 'Isi manual koordinat longitude'
                                    )
                                    ->required(),

                            ])
                            ->columns(2),

                        Radio::make('location_type')
                            ->label('Letak Titik Menara')
                            ->options([
                                'jpo' => 'JPO',
                                'jpm' => 'JPM',
                                'flyover' => 'Flyover',
                                'underpass' => 'Underpass',
                                'pedestrian' => 'Pedestrian',
                                'rth' => 'RTH',
                            ])
                            ->inline(),

                        Radio::make('tower_type')
                            ->label('Jenis Menara')
                            ->options([
                                'pole' => 'Pole',
                                'rangka' => 'Rangka',
                            ])
                            ->inline(),

                        TextInput::make('observation_distance')
                            ->label('Jarak Pengamatan (m)')
                            ->numeric(),
                    ])
                    ->columns(2),

                // =========================
                // SECTION 2 — STRUKTUR PENGAIT
                // =========================
                Section::make('Struktur Pengait Rangka')
                    ->schema([

                        Radio::make('bolt_count')
                            ->label('Jumlah Mur/Baut Pengait')
                            ->options([
                                'tidak_lengkap' => 'Tidak Lengkap',
                                'lengkap' => 'Lengkap',
                                'tidak_terlihat' => 'Tidak Terlihat',
                            ])
                            ->inline(),

                        Radio::make('bolt_condition')
                            ->label('Kondisi Mur/Baut Pengait')
                            ->options([
                                'berkarat' => 'Berkarat',
                                'tidak_berkarat' => 'Tidak Berkarat',
                                'tidak_terlihat' => 'Tidak Terlihat',
                            ])
                            ->inline(),

                        Radio::make('bolt_position')
                            ->label('Posisi Pasangan Mur/Baut Pengait')
                            ->options([
                                'longgar' => 'Longgar',
                                'tidak_longgar' => 'Tidak Longgar',
                                'tidak_terlihat' => 'Tidak Terlihat',
                            ])
                            ->inline(),
                    ]),

                // =========================
                // SECTION 3 — STRUKTUR RANGKA
                // =========================
                Section::make('Struktur Rangka Menara')
                    ->schema([

                        // 🔹 Kondisi Rangka
                        Radio::make('frame_condition')
                            ->label('Kondisi Rangka Menara')
                            ->options([
                                'miring' => 'Miring',
                                'tegak' => 'Tegak',
                            ])
                            ->inline(),

                        // 🔹 Maintenance
                        Radio::make('frame_maintenance')
                            ->label('----------------------------------------------------------------------------------')
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                            ])
                            ->inline(),

                        // 🔹 Rust
                        Radio::make('frame_rust')
                            ->label('----------------------------------------------------------------------------------')
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                            ])
                            ->inline(),

                        // 🔹 Porous
                        Radio::make('frame_porous')
                            ->label('----------------------------------------------------------------------------------')
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                            ])
                            ->inline(),

                        // 🔹 Joint Maintenance
                        Radio::make('joint_maintenance')
                            ->label('Sambungan Rangka Utama')
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline(),

                        // 🔹 Joint Rust
                        Radio::make('joint_rust')
                            ->label('----------------------------------------------------------------------------------')
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline(),

                        // 🔹 Joint Porous
                        Radio::make('joint_porous')
                            ->label('----------------------------------------------------------------------------------')
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline(),

                    ])
                    ->columns(1),

                // =========================
                // SECTION 4 — KONDISI PANEL
                // =========================
                Section::make('Kondisi Bidang Menara')
                    ->schema([

                        Radio::make('panel_structure')
                            ->label('Struktur Panel')
                            ->options([
                                'not_connected_well' => 'Tidak Tersambung Baik Pada Pada Tiang Utama',
                                'connected_well' => 'Tersambung Baik Pada Tiang Utama',
                            ])
                            ->inline(),

                        Radio::make('panel_status')
                            ->label('Bidang Panel')
                            ->options([
                                'loose' => 'Ada yang Lepas',
                                'no_loose' => 'Tidak Ada Lepas',
                                'no_panel' => 'Tidak Ada Bidang Panel',
                            ])
                            ->inline(),

                        Radio::make('lamp_frame')
                            ->label('Rangka Lampu')
                            ->options([
                                'not_connected_well' => 'Tidak Tersambung Dengan Baik Pada Struktur Panel',
                                'connected_well' => 'Tersambung Dengan Baik Pada Struktur Panel',
                                'no_lamp' => 'Tidak Ada Lampu',
                            ])
                            ->inline(),
                    ]),

                // =========================
                // SECTION 5 — CATATAN
                // =========================
                Section::make('Catatan')
                    ->schema([
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),

                // =========================
                // SECTION 6 — KESIMPULAN
                // =========================
                Section::make('Kesimpulan')
                    ->schema([

                        Radio::make('construction_feasibility')
                            ->label('Kondisi Kelayakan Konstruksi Menara')
                            ->options([
                                'dangerous' => 'Berpotensi Membahayakan',
                                'not_dangerous' => 'Tidak Membahayakan',
                            ])
                            ->inline(),

                        Radio::make('follow_up_action')
                            ->label('Tindak lanjut')
                            ->options([
                                'enforcement_proposal' => 'Penertiban',
                                'periodic_monitoring' => 'Monitoring',
                            ])
                            ->inline(),
                    ]),

                // =========================
                // SECTION 7 — FOTO
                // =========================
                Section::make('Foto Lapangan')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Foto')
                                    ->image()
                                    ->disk('public')
                                    ->directory('field-inspections')
                                    ->visibility('public')
                                    ->imagePreviewHeight('150')
                                    ->formatStateUsing(fn ($state) => $state)
                                    ->dehydrated(true)
                                    ->required(),

                                TextInput::make('caption')
                                    ->label('Keterangan'),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addActionLabel('Tambah Foto')
                    ]),
            ]);
    }
}
