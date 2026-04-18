<?php

namespace App\Exports;

use App\Models\Visit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $towerId;
    protected $userId;
    protected $kecamatan;

    public function __construct($startDate, $endDate, $towerId = null, $userId = null, $kecamatan = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->towerId = $towerId;
        $this->userId = $userId;
        $this->kecamatan = $kecamatan;
    }

    public function query()
    {
        $query = Visit::query()
            ->with(['tower', 'creator'])
            ->whereBetween('inspection_date', [$this->startDate, $this->endDate]);

        if ($this->towerId) {
            $query->where('tower_id', $this->towerId);
        }

        if ($this->userId) {
            $query->where('created_by', $this->userId);
        }

        if ($this->kecamatan) {
            $query->whereHas('tower', function ($q) {
                $q->where('kecamatan', $this->kecamatan);
            });
        }

        return $query->orderBy('inspection_date', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Menara',
            'Tanggal',
            'Lokasi Menara',
            'Kelurahan',
            'Letak Titik Menara',
            'Jumlah Mur/Baut Pengait',
            'Kondisi Mur/Baut Pengait',
            'Posisi Pasangan Mur/Baut Pengait',
            'Kondisi Rangka',
            'Sambungan Rangka Utama',
            'Struktur Panel',
            'Bidang Panel',
            'Rangka Lampu',
            'Kelayakan Konstruksi & Tindak Lanjut',
            'Latitude',
            'Longitude',
            'Pemeriksa',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $tower = $item->tower;

        return [
            $no,
            $tower?->tower_id ?? '-',
            $item->inspection_date,
            ($tower?->location_name ?? '-') . ($tower?->location_detail ? " ({$tower->location_detail})" : ""),
            $tower?->kelurahan ?? '-',
            ['jpo' => 'JPO', 'jpm' => 'JPM', 'flyover' => 'Flyover', 'underpass' => 'Underpass', 'pedestrian' => 'Pedestrian', 'rth' => 'RTH'][$item->location_type] ?? ucfirst($item->location_type),
            $this->formatLabel($item->bolt_count),
            $this->formatLabel($item->bolt_condition),
            $this->formatLabel($item->bolt_position),
            $this->formatFrame($item),
            $this->formatJoint($item),
            $item->panel_structure === 'connected_well' ? 'Tersambung Baik Pada tiang utama' : 'Tidak Tersambung Baik Pada tiang utama',
            $this->formatPanelStatus($item->panel_status),
            $this->formatLampFrame($item->lamp_frame),
            ($item->construction_feasibility === 'dangerous' ? 'Berpotensi Membahayakan' : 'Tidak Berpotensi Membahayakan') . ', ' . ($item->follow_up_action === 'enforcement_proposal' ? 'Segera Usul Penertiban' : 'Monitor Berkala'),
            is_numeric($tower?->latitude ?? null) ? (str_starts_with($tower->latitude, '-') ? number_format((float)$tower->latitude, 6, '.', '') : '-' . number_format((float)$tower->latitude, 6, '.', '')) : ($tower?->latitude ?? '-'),
            is_numeric($tower?->longitude ?? null) ? number_format((float)$tower->longitude, 6, '.', '') : ($tower?->longitude ?? '-'),
            $item->creator?->name ?? 'System',
        ];
    }

    private function formatLabel($value)
    {
        return match($value) {
            'lengkap' => 'Lengkap',
            'tidak_lengkap' => 'Tidak Lengkap',
            'berkarat' => 'Berkarat',
            'tidak_berkarat' => 'Tidak Berkarat',
            'longgar' => 'Longgar',
            'tidak_longgar' => 'Tidak Longgar',
            default => 'Tidak Terlihat',
        };
    }

    private function formatFrame($item)
    {
        $cond = $item->frame_condition === 'tegak' ? 'Tegak' : 'Miring';
        $maint = $item->frame_maintenance === 'maintained' ? 'Terpelihara (dicat)' : 'Tidak Terpelihara (tidak dicat)';
        $rust = $item->frame_rust === 'rusted' ? 'Berkarat' : 'Tidak Berkarat';
        $porous = $item->frame_porous === 'porous' ? 'Keropos' : 'Tidak Keropos';
        return "{$cond}, {$maint}, {$rust}, {$porous}";
    }

    private function formatJoint($item)
    {
        $maint = match($item->joint_maintenance) {
            'maintained' => 'Terpelihara (dicat)',
            'not_maintained' => 'Tidak Terpelihara (tidak dicat)',
            default => 'Tidak Terlihat',
        };
        $rust = match($item->joint_rust) {
            'rusted' => 'Berkarat',
            'not_rusted' => 'Tidak Berkarat',
            default => 'Tidak Terlihat',
        };
        $porous = match($item->joint_porous) {
            'porous' => 'Keropos',
            'not_porous' => 'Tidak Keropos',
            default => 'Tidak Terlihat',
        };
        return "{$maint}, {$rust}, {$porous}";
    }

    private function formatPanelStatus($value)
    {
        return match($value) {
            'no_loose' => 'Tidak Ada yang lepas',
            'loose' => 'Ada yang lepas',
            default => 'Tidak Ada Bidang Panel',
        };
    }

    private function formatLampFrame($value)
    {
        return match($value) {
            'connected_well' => 'Tersambung Baik',
            'not_connected_well' => 'Tidak Tersambung Baik',
            default => 'Tidak Ada Lampu',
        };
    }
}
