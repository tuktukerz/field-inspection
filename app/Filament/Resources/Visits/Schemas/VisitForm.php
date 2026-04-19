<?php

namespace App\Filament\Resources\Visits\Schemas;

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
use App\Models\Tower;
use Filament\Resources\RelationManagers\RelationManager;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Inspeksi')
                    ->schema([
                        Select::make('tower_id')
                            ->label('Pilih Menara')
                            ->relationship('tower', 'location_name')
                            ->getOptionLabelFromRecordUsing(fn (Tower $record) => "{$record->location_name} ({$record->location_detail}, Kel. {$record->kelurahan}, Kec. {$record->kecamatan})")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->hidden(fn ($livewire) => $livewire instanceof RelationManager),

                        Placeholder::make('tower_address')
                            ->label('Detail Alamat')
                            ->content(function ($get) {
                                $towerId = $get('tower_id');
                                if (!$towerId) return '-';
                                $tower = Tower::find($towerId);
                                if (!$tower) return '-';
                                return "{$tower->location_name} ({$tower->location_detail}) - Kel. {$tower->kelurahan}, Kec. {$tower->kecamatan}";
                            })
                            ->visible(fn ($get) => filled($get('tower_id')))
                            ->columnSpanFull(),

                        DatePicker::make('inspection_date')
                            ->label('Tanggal Inspeksi')
                            ->required(),

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
                            ->label('Jarak Pengamatan (meter)')
                            ->numeric()
                            ->required(),

                        Placeholder::make('visited_by')
                            ->label('Pemeriksa')
                            ->content(fn ($record) => $record?->creator?->name ?? '-'),
                    ])->columns(2),

                Section::make('Struktur Pengait Menara')
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

                Section::make('Struktur Rangka Menara')
                    ->schema([
                        Radio::make('frame_condition')
                            ->label('Kondisi Rangka Menara')
                            ->options([
                                'miring' => 'Miring',
                                'tegak' => 'Tegak',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('frame_maintenance')
                            ->label('Pemeliharaan Rangka Menara')
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('frame_rust')
                            ->label('Karat Rangka Menara')
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('frame_porous')
                            ->label('Keropos Rangka Menara')
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('joint_maintenance')
                            ->label('Sambungan Rangka Utama (Pemeliharaan)')
                            ->options([
                                'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
                                'maintained' => 'Terpelihara (dicat)',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('joint_rust')
                            ->label('Sambungan Rangka Utama (Karat)')
                            ->options([
                                'rusted' => 'Berkarat',
                                'not_rusted' => 'Tidak Berkarat',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('joint_porous')
                            ->label('Sambungan Rangka Utama (Keropos)')
                            ->options([
                                'porous' => 'Keropos',
                                'not_porous' => 'Tidak Keropos',
                                'not_visible' => 'Tidak Terlihat',
                            ])
                            ->inline()
                            ->required(),
                    ])->columns(1),

                Section::make('Kondisi Bidang Menara')
                    ->schema([
                        Radio::make('panel_structure')
                            ->label('Struktur Panel')
                            ->options([
                                'not_connected_well' => 'Tidak Tersambung Baik Pada tiang utama',
                                'connected_well' => 'Tersambung Baik Pada tiang utama',
                                'no_panel_structure' => 'Tidak Ada Struktur Panel',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('panel_status')
                            ->label('Bidang Panel')
                            ->options([
                                'loose' => 'Ada yang Lepas',
                                'no_loose' => 'Tidak Ada yang Lepas',
                                'no_panel' => 'Tidak Ada Bidang Panel',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('lamp_frame')
                            ->label('Rangka Lampu')
                            ->options([
                                'not_connected_well' => 'Tidak Tersambung Baik',
                                'connected_well' => 'Tersambung Baik',
                                'no_lamp' => 'Tidak Ada Lampu',
                            ])
                            ->inline()
                            ->required(),
                    ]),

                Section::make('Catatan & Kesimpulan')
                    ->schema([
                        Textarea::make('notes')->columnSpanFull(),

                        Radio::make('construction_feasibility')
                            ->label('Kondisi Kelayakan Konstruksi Menara')
                            ->options([
                                'dangerous' => 'Berpotensi Membahayakan',
                                'not_dangerous' => 'Tidak Berpotensi Membahayakan',
                            ])
                            ->inline()
                            ->required(),

                        Radio::make('follow_up_action')
                            ->label('Tindak Lanjut')
                            ->options([
                                'enforcement_proposal' => 'Segera Usul Penertiban',
                                'periodic_monitoring' => 'Monitor Berkala',
                            ])
                            ->inline()
                            ->required(),
                    ]),

                Section::make('Foto Inspeksi')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->required()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Foto')
                                    ->image()
                                    ->imagePreviewHeight('220')
                                    ->panelLayout('integrated')
                                    ->maxSize(10240)
                                    ->imageResizeTargetWidth('1280')
                                    ->imageResizeTargetHeight('1280')
                                    ->disk('public')
                                    ->directory('visit-images')
                                    ->visibility('public')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(fn ($file) =>
                                        \App\Services\ImageCompressor::compressAndStore($file, 'visit-images')
                                    )
                                    ->required(),

                                TextInput::make('caption')
                                    ->label('Keterangan'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah Foto')
                    ]),
            ]);
    }
}
