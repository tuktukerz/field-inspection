<?php

namespace App\Filament\Widgets;

use App\Models\Tower;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class InspectionChart extends Widget
{
    protected string $view = 'filament.widgets.inspection-chart';

    protected static ?int $sort = 3;

    public function getChartData(): array
    {
        $rows = Tower::query()
            ->select('kecamatan', DB::raw('count(*) as count'))
            ->whereNotNull('kecamatan')
            ->groupBy('kecamatan')
            ->orderByDesc('count')
            ->get();

        $total = (int) $rows->sum('count');

        $palette = [
            ['from' => '#38bdf8', 'to' => '#0369a1', 'solid' => '#0ea5e9'],
            ['from' => '#34d399', 'to' => '#059669', 'solid' => '#10b981'],
            ['from' => '#fbbf24', 'to' => '#d97706', 'solid' => '#f59e0b'],
            ['from' => '#a78bfa', 'to' => '#6d28d9', 'solid' => '#8b5cf6'],
            ['from' => '#f472b6', 'to' => '#be185d', 'solid' => '#ec4899'],
            ['from' => '#2dd4bf', 'to' => '#0f766e', 'solid' => '#14b8a6'],
            ['from' => '#fb923c', 'to' => '#c2410c', 'solid' => '#f97316'],
            ['from' => '#818cf8', 'to' => '#4338ca', 'solid' => '#6366f1'],
            ['from' => '#a3e635', 'to' => '#4d7c0f', 'solid' => '#84cc16'],
            ['from' => '#22d3ee', 'to' => '#0e7490', 'solid' => '#06b6d4'],
        ];

        $cumulative = 0;
        $segments = [];
        foreach ($rows as $i => $row) {
            $count = (int) $row->count;
            $pct = $total > 0 ? ($count / $total) * 100 : 0;
            $colors = $palette[$i % count($palette)];
            $segments[] = [
                'label' => $row->kecamatan,
                'count' => $count,
                'percent' => round($pct, 1),
                'start' => $cumulative,
                'end' => $cumulative + $pct,
                'solid' => $colors['solid'],
                'from' => $colors['from'],
                'to' => $colors['to'],
            ];
            $cumulative += $pct;
        }

        return [
            'total' => $total,
            'segments' => $segments,
        ];
    }
}
