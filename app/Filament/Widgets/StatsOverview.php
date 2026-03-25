<?php

namespace App\Filament\Widgets;

use App\Models\Tower;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Menara', Tower::count())
                ->description('Jumlah menara terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Total Inspeksi', Visit::count())
                ->description('Seluruh riwayat patroli')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),
            Stat::make('Inspeksi Bulan Ini', Visit::whereMonth('inspection_date', now()->month)
                ->whereYear('inspection_date', now()->year)
                ->count())
                ->description('Aktivitas patroli bulan ' . now()->isoFormat('MMMM'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}
