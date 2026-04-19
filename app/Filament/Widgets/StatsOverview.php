<?php

namespace App\Filament\Widgets;

use App\Models\Tower;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';

    protected int | string | array $columnSpan = 'full';

    public function getStatsData(): array
    {
        $totalMenara = Tower::count();
        $totalInspeksi = Visit::count();
        $bulanIni = Visit::whereMonth('inspection_date', now()->month)
            ->whereYear('inspection_date', now()->year)
            ->count();
        $bulanLalu = Visit::whereMonth('inspection_date', now()->subMonth()->month)
            ->whereYear('inspection_date', now()->subMonth()->year)
            ->count();

        $trendBulan = 0;
        if ($bulanLalu > 0) {
            $trendBulan = round((($bulanIni - $bulanLalu) / $bulanLalu) * 100);
        } elseif ($bulanIni > 0) {
            $trendBulan = 100;
        }

        return [
            'total_menara' => $totalMenara,
            'total_inspeksi' => $totalInspeksi,
            'inspeksi_bulan_ini' => $bulanIni,
            'bulan_label' => now()->isoFormat('MMMM'),
            'trend_bulan' => $trendBulan,
        ];
    }
}
