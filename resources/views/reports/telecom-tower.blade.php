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
        Sektor Dinas Cipta Karya, Tata Ruang dan Pertanahan Kecamatan Sawah Besar<br>
        {{ $quarter }} Tahun {{ $year }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-col">No.</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 140px;">Lokasi</th>
                <th style="width: 80px;">Kelurahan</th>
                <th style="width: 60px;">Letak Titik Menara</th>
                <th style="width: 50px;">Jumlah Mur</th>
                <th style="width: 50px;">Kondisi Mur</th>
                <th style="width: 60px;">Posisi Pasangan Mur</th>
                <th style="width: 70px;">Kondisi Rangka</th>
                <th style="width: 80px;">Sambungan Rangka Utama</th>
                <th style="width: 70px;">Struktur Panel</th>
                <th style="width: 60px;">Bidang Panel</th>
                <th style="width: 60px;">Rangka Lampu</th>
                <th style="width: 130px;">Koordinat</th>
                <th style="width: 130px;">Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $index => $item)
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->inspection_date)->isoFormat('D MMMM YYYY') }}</td>
                    <td class="text-left">
                        <strong>{{ $item->location_name }}</strong><br>
                        <small>{{ $item->location_detail }}</small>
                    </td>
                    <td>{{ $item->kelurahan }}</td>
                    <td>{{ strtoupper($item->location_type) }}</td>
                    <td>
                        {{ $item->bolt_count === 'lengkap' ? 'Lengkap' : ($item->bolt_count === 'tidak_lengkap' ? 'Tidak Lengkap' : 'Tidak Terlihat') }}
                    </td>
                    <td>
                        {{ $item->bolt_condition === 'berkarat' ? 'Berkarat' : ($item->bolt_condition === 'tidak_berkarat' ? 'Tidak Berkarat' : 'Tidak Terlihat') }}
                    </td>
                    <td>
                        {{ $item->bolt_position === 'longgar' ? 'Longgar' : ($item->bolt_position === 'tidak_longgar' ? 'Tidak Longgar' : 'Tidak Terlihat') }}
                    </td>
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
                    <td>
                        {{ $item->panel_structure === 'connected_well' ? 'Tersambung Baik Pada Tiang Utama' : 'Tidak Tersambung Baik Pada Tiang Utama' }}
                    </td>
                    <td>
                        {{ $item->panel_status === 'no_loose' ? 'Tidak Ada yang lepas' : ($item->panel_status === 'loose' ? 'Ada yang lepas' : 'Tidak Ada Bidang Panel') }}
                    </td>
                    <td>
                        {{ $item->lamp_frame === 'connected_well' ? 'Tersambung dengan Baik pada Struktur Panel' : ($item->lamp_frame === 'not_connected_well' ? 'Tidak Tersambung dengan Baik pada Struktur Panel' : 'Tidak Ada Lampu') }}
                    </td>
                    <td>
                        {{ is_numeric($item->latitude) ? number_format((float)$item->latitude, 6, '.', '') : $item->latitude }}<br>
                        {{ is_numeric($item->longitude) ? number_format((float)$item->longitude, 6, '.', '') : $item->longitude }}
                    </td>
                    <td>
                        @if($item->images->first())
                            @php
                                $path = storage_path('app/public/' . $item->images->first()->image_path);
                                if (!file_exists($path)) {
                                    $path = public_path('storage/' . $item->images->first()->image_path);
                                }
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                if (file_exists($path)) {
                                    $data = file_get_contents($path);
                                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                } else {
                                    $base64 = null;
                                }
                            @endphp
                            @if($base64)
                                <img src="{{ $base64 }}" style="width: 120px;">
                            @else
                                <small>Foto tidak ditemukan</small>
                            @endif
                        @else
                            <small>Tidak ada foto</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
