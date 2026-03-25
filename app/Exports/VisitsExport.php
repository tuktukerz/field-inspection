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

    public function __construct($startDate, $endDate, $towerId = null, $userId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->towerId = $towerId;
        $this->userId = $userId;
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

        return $query->orderBy('inspection_date', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tower ID',
            'Tanggal',
            'Lokasi',
            'Kelurahan',
            'Letak Titik Menara',
            'Jumlah Mur',
            'Kondisi Mur',
            'Posisi Pasangan Mur',
            'Kondisi Rangka',
            'Sambungan Rangka Utama',
            'Struktur Panel',
            'Bidang Panel',
            'Rangka Lampu',
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
            strtoupper($item->location_type),
            $this->formatLabel($item->bolt_count),
            $this->formatLabel($item->bolt_condition),
            $this->formatLabel($item->bolt_position),
            $this->formatFrame($item),
            $this->formatJoint($item),
            $item->panel_structure === 'connected_well' ? 'Tersambung Baik' : 'Tidak Tersambung Baik',
            $this->formatPanelStatus($item->panel_status),
            $this->formatLampFrame($item->lamp_frame),
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
