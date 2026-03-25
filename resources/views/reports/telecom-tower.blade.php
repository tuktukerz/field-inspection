<!DOCTYPE html>
<html>
<head>
    <title>Laporan Menara Telekomunikasi</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
            vertical-align: middle;
        }
        th {
            background-color: #BDD7EE;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .no-col {
            width: 25px !important;
            padding-left: 1px !important;
            padding-right: 1px !important;
            font-size: 8pt !important;
        }
        .img-container {
            width: 120px;
            height: 90px;
        }
        img {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        Laporan Menara Telekomunikasi<br>
        {{ $quarter }} Tahun {{ $year }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="no-col">No</th>
                <th rowspan="2" style="width: 70px;">ID Menara</th>
                <th rowspan="2" style="width: 100px;">Lokasi</th>
                <th rowspan="2" style="width: 80px;">Wilayah</th>
                <th rowspan="2" style="width: 120px;">Koordinat</th>
                <th rowspan="2" style="width: 65px;">Tanggal</th>
                <th rowspan="2" style="width: 70px;">Pemeriksa</th>
                <th rowspan="2" style="width: 50px;">Letak Titik menara</th>
                <th colspan="3">Struktur Pengait Rangka</th>
                <th colspan="2">Struktur Rangka Menara</th>
                <th colspan="3">Kondisi Bidang Menara</th>
                <th rowspan="2" style="width: 100px;">Foto</th>
            </tr>
            <tr>
                <th style="width: 45px;">Jumlah Mur/Baut</th>
                <th style="width: 50px;">Kondisi Mur/Baut</th>
                <th style="width: 55px;">Posisi Pasangan Mur/Baut</th>
                <th style="width: 55px;">Kondisi Rangka Menara*</th>
                <th style="width: 70px;">Sambungan Rangka Utama (Pemeliharaan)</th>
                <th style="width: 55px;">Struktur Panel</th>
                <th style="width: 50px;">Bidang Panel</th>
                <th style="width: 50px;">Rangka Lampu</th>
            </tr>
        </thead>
        <tbody>
            @php
                $towerGroups = $inspections->groupBy('tower_id');
                $towerIndex = 1;
            @endphp

            @foreach($towerGroups as $towerId => $group)
                @foreach($group as $visitIndex => $item)
                    <tr>
                        @if($visitIndex === 0)
                            <td class="no-col" rowspan="{{ $group->count() }}">{{ $towerIndex++ }}</td>
                            <td rowspan="{{ $group->count() }}">{{ $item->tower?->tower_id ?? '-' }}</td>
                            <td class="text-left" rowspan="{{ $group->count() }}">
                                <strong>{{ $item->tower?->location_name ?? '-' }}</strong><br>
                                <small>{{ $item->tower?->location_detail ?? '-' }}</small>
                            </td>
                            <td rowspan="{{ $group->count() }}">
                                <strong>{{ $item->tower?->kecamatan ?? '-' }}</strong><br>
                                <small>{{ $item->tower?->kelurahan ?? '-' }}</small>
                            </td>
                            <td rowspan="{{ $group->count() }}" style="font-size: 7.5pt; white-space: nowrap;">
                                @php
                                    $lat = $item->tower?->latitude;
                                    $lat = str_starts_with($lat ?? '', '-') ? $lat : '-' . $lat;
                                    $lat = is_numeric($lat) ? number_format((float)$lat, 7, '.', '') : $lat;
                                    
                                    $lng = $item->tower?->longitude;
                                    $lng = is_numeric($lng) ? number_format((float)$lng, 7, '.', '') : $lng;
                                @endphp
                                {{ $lat }}<br>
                                {{ $lng }}
                            </td>
                        @endif

                        <td>{{ \Carbon\Carbon::parse($item->inspection_date)->isoFormat('DD/MM/YYYY') }}</td>
                        <td>{{ $item->creator?->name ?? 'System' }}</td>
                        <td>{{ strtoupper($item->location_type) }}</td>
                        
                        {{-- Struktur Pengait Rangka --}}
                        <td>{{ $item->bolt_count === 'lengkap' ? 'Lengkap' : ($item->bolt_count === 'tidak_lengkap' ? 'Tidak Lengkap' : 'Tidak Terlihat') }}</td>
                        <td>{{ $item->bolt_condition === 'berkarat' ? 'Berkarat' : ($item->bolt_condition === 'tidak_berkarat' ? 'Normal' : 'Tidak Terlihat') }}</td>
                        <td>{{ $item->bolt_position === 'longgar' ? 'Longgar' : ($item->bolt_position === 'tidak_longgar' ? 'Kencang' : 'Tidak Terlihat') }}</td>

                        {{-- Struktur Rangka Menara --}}
                        <td>
                            {{ $item->frame_condition === 'tegak' ? 'Tegak' : 'Miring' }}, 
                            {{ $item->frame_maintenance === 'maintained' ? 'Terpelihara (dicat)' : 'Tidak Terpelihara (tidak dicat)' }},
                            {{ $item->frame_rust === 'rusted' ? 'Berkarat' : 'Tidak Berkarat' }},
                            {{ $item->frame_porous === 'porous' ? 'Keropos' : 'Tidak Keropos' }}
                        </td>
                        <td>
                            {{ $item->joint_maintenance === 'maintained' ? 'Terpelihara (dicat)' : ($item->joint_maintenance === 'not_maintained' ? 'Tidak Terpelihara (tidak dicat)' : 'Tidak Terlihat') }},
                            {{ $item->joint_rust === 'rusted' ? 'Berkarat' : ($item->joint_rust === 'not_rusted' ? 'Tidak Berkarat' : 'Tidak Terlihat') }},
                            {{ $item->joint_porous === 'porous' ? 'Keropos' : ($item->joint_porous === 'not_porous' ? 'Tidak Keropos' : 'Tidak Terlihat') }}
                        </td>

                        {{-- Kondisi Bidang Menara --}}
                        <td>{{ $item->panel_structure === 'connected_well' ? 'Tersambung Baik' : 'Tidak Tersambung Baik' }}</td>
                        <td>{{ $item->panel_status === 'no_loose' ? 'Tidak Ada yang lepas' : ($item->panel_status === 'loose' ? 'Ada yang lepas' : 'Tidak Ada Bidang Panel') }}</td>
                        <td>{{ $item->lamp_frame === 'connected_well' ? 'Tersambung Baik' : ($item->lamp_frame === 'not_connected_well' ? 'Tidak Tersambung Baik' : 'Tidak Ada Lampu') }}</td>

                        <td>
                            @if($item->images->count() > 0)
                                @php
                                    $path = storage_path('app/public/' . $item->images->first()->image_path);
                                    if (!file_exists($path)) $path = public_path('storage/' . $item->images->first()->image_path);
                                    $base64 = null;
                                    if (file_exists($path)) {
                                        $data = file_get_contents($path);
                                        $base64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
                                    }
                                @endphp
                                @if($base64)
                                    <img src="{{ $base64 }}" style="width: 80px; height: 60px; object-fit: cover;">
                                @else
                                    <small>No File</small>
                                @endif
                            @else
                                <small>-</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
