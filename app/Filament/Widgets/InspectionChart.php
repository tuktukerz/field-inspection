<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class InspectionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Inspeksi Per Kecamatan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Visit::query()
            ->join('towers', 'visits.tower_id', '=', 'towers.id')
            ->select('towers.kecamatan', DB::raw('count(*) as count'))
            ->groupBy('towers.kecamatan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Inspeksi',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                    ],
                ],
            ],
            'labels' => $data->pluck('kecamatan')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
