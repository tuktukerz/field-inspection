<?php

namespace App\Filament\Widgets;

use App\Models\Tower;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class InspectionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Menara Per Kecamatan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Tower::query()
            ->select('kecamatan', DB::raw('count(*) as count'))
            ->groupBy('kecamatan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Menara',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6',
                        '#f97316', '#6366f1', '#84cc16', '#06b6d4',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('kecamatan')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
