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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\FieldInspection;
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
                        Select::make('revisit_from_inspection')
                            ->label('Pilih dari Inspeksi Sebelumnya (untuk kunjungan ulang)')
                            ->placeholder('Cari berdasarkan nama lokasi atau nomor dokumen...')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                FieldInspection::where('location_name', 'like', "%{$search}%")
                                    ->orWhere('document_number', 'like', "%{$search}%")
                                    ->limit(10)
                                    ->get(['id', 'location_name', 'document_number'])
                                    ->mapWithKeys(fn ($item) => [$item->id => "{$item->location_name} ({$item->document_number})"])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => FieldInspection::find($value)?->location_name)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (!$state) return;
                                
                                $previous = FieldInspection::find($state);
                                if (!$previous) return;

                                // Pre-fill tower metadata
                                $set('location_name', $previous->location_name);
                                $set('location_detail', $previous->location_detail);
                                $set('kelurahan', $previous->kelurahan);
                                $set('kecamatan', $previous->kecamatan);
                                $set('latitude', $previous->latitude);
                                $set('longitude', $previous->longitude);
                                $set('location_type', $previous->location_type);
                                $set('tower_type', $previous->tower_type);
                                $set('observation_distance', $previous->observation_distance);
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        DatePicker::make('inspection_date')
                            ->label('Tanggal Inspeksi')
                            ->required(),

                        TextInput::make('location_name')
                            ->label('Lokasi Menara')
                            ->required(),

                        Textarea::make('location_detail')
                            ->label('Lokasi Detail')
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
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('latitude', null);
                                        $set('longitude', null);
                                    })
                                    ->live(),

                                // 🔹 BUTTON GPS
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

                                // 🔹 LATITUDE
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->placeholder('-6.200000')
                                    ->readOnly(fn (Get $get) => $get('location_mode') === 'gps')
                                    ->helperText(fn (Get $get) =>
                                    $get('location_mode') === 'gps'
                                        ? 'Akan terisi otomatis dari GPS'
                                        : 'Isi manual koordinat latitude'
                                    )
                                    ->required(),

                                // 🔹 LONGITUDE
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->placeholder('106.816666')
                                    ->readOnly(fn (Get $get) => $get('location_mode') === 'gps')
                                    ->helperText(fn (Get $get) =>
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
                            ->inline()
                            ->required(),

                        Radio::make('tower_type')
                            ->label('Jenis Menara')
                            ->options([
                                'pole' => 'Pole',
                                'rangka' => 'Rangka',
                            ])
                            ->inline()
                            ->required(),

                        TextInput::make('observation_distance')
                            ->label('Jarak Pengamatan (m)')
                            ->numeric()
                            ->required(),
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
                            ->inline()
                            ->required(),

                        Radio::make('bolt_condition')
                            ->label('Kondisi Mur/Baut Pengait')
                            ->options([
                                'berkarat' => 'Berkarat',
                                'tidak_berkarat' => 'Tidak Berkarat',
                                'tidak_terlihat' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('bolt_position')
                            ->label('Posisi Pasangan Mur/Baut Pengait')
                            ->options([
                                'longgar' => 'Longgar',
                                'tidak_longgar' => 'Tidak Longgar',
                                'tidak_terlihat' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),
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
                            ->inline()
                            ->required(),

                        // 🔹 Maintenance
                        Radio::make('frame_maintenance')
                            ->hiddenLabel()
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                            ])
                            ->inline()
                            ->required(),

                        // 🔹 Rust
                        Radio::make('frame_rust')
                            ->hiddenLabel()
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                            ])
                            ->inline()
                            ->required(),

                        // 🔹 Porous
                        Radio::make('frame_porous')
                            ->hiddenLabel()
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                            ])
                            ->inline()
                            ->required(),

                        // 🔹 Joint Maintenance
                        Radio::make('joint_maintenance')
                            ->label('Sambungan Rangka Utama')
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

                        // 🔹 Joint Rust
                        Radio::make('joint_rust')
                            ->hiddenLabel()
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

                        // 🔹 Joint Porous
                        Radio::make('joint_porous')
                            ->hiddenLabel()
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

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
                            ->inline()
                            ->required(),

                        Radio::make('panel_status')
                            ->label('Bidang Panel')
                            ->options([
                                'loose' => 'Ada yang Lepas',
                                'no_loose' => 'Tidak Ada Lepas',
                                'no_panel' => 'Tidak Ada Bidang Panel',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('lamp_frame')
                            ->label('Rangka Lampu')
                            ->options([
                                'not_connected_well' => 'Tidak Tersambung Dengan Baik Pada Struktur Panel',
                                'connected_well' => 'Tersambung Dengan Baik Pada Struktur Panel',
                                'no_lamp' => 'Tidak Ada Lampu',
                            ])
                            ->inline()
                            ->required(),
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
                            ->inline()
                            ->required(),

                        Radio::make('follow_up_action')
                            ->label('Tindak lanjut')
                            ->options([
                                'enforcement_proposal' => 'Penertiban',
                                'periodic_monitoring' => 'Monitoring',
                            ])
                            ->inline()
                            ->required(),
                    ]),

                // =========================
                // SECTION 7 — FOTO
                // =========================
                Section::make('Foto Lapangan')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->required()
                            ->schema([
                                FileUpload::make('image_path')
                                ->label('Foto')
                                ->image()
                                ->disk('public')
                                ->directory('field-inspections')
                                ->visibility('public')
                                ->imagePreviewHeight('150')
                                ->dehydrated(true)
                                ->required()
                                ->downloadable()
                                ->openable(),

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
