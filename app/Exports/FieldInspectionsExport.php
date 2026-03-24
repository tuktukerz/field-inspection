<?php

namespace App\Exports;

use App\Models\FieldInspection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FieldInspectionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return FieldInspection::query()
            ->whereBetween('inspection_date', [$this->startDate, $this->endDate])
            ->orderBy('inspection_date', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
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
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->inspection_date,
            $item->location_name . ($item->location_detail ? " ({$item->location_detail})" : ""),
            $item->kelurahan,
            strtoupper($item->location_type),
            $this->formatLabel($item->bolt_count),
            $this->formatLabel($item->bolt_condition),
            $this->formatLabel($item->bolt_position),
            $this->formatFrame($item),
            $this->formatJoint($item),
            $item->panel_structure === 'connected_well' ? 'Tersambung Baik' : 'Tidak Tersambung Baik',
            $this->formatPanelStatus($item->panel_status),
            $this->formatLampFrame($item->lamp_frame),
            is_numeric($item->latitude) ? number_format((float)$item->latitude, 6, '.', '') : $item->latitude,
            is_numeric($item->longitude) ? number_format((float)$item->longitude, 6, '.', '') : $item->longitude,
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
        $maint = $item->frame_maintenance === 'maintained' ? 'Terpelihara' : 'Tidak Terpelihara';
        $rust = $item->frame_rust === 'rusted' ? 'Berkarat' : 'Tidak Berkarat';
        $porous = $item->frame_porous === 'porous' ? 'Keropos' : 'Tidak Keropos';
        return "{$cond}, {$maint}, {$rust}, {$porous}";
    }

    private function formatJoint($item)
    {
        $maint = match($item->joint_maintenance) {
            'maintained' => 'Terpelihara',
            'not_maintained' => 'Tidak Terpelihara',
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
