<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Tower;

class MapDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Peta Menara';
    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Peta Menara';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'SIMTEL',
            'Peta Menara',
        ];
    }

    protected string $view = 'filament.pages.map-dashboard';

    public function getStats(): array
    {
        $towers = Tower::with(['visits' => fn ($q) => $q->latest()->limit(1)])->get();

        $total = $towers->count();
        $terpantau = 0;
        $perluPemeriksaan = 0;
        $prioritasTinggi = 0;

        foreach ($towers as $tower) {
            $latest = $tower->visits->first();
            if (! $latest) {
                $prioritasTinggi++;
                continue;
            }
            $days = \Carbon\Carbon::parse($latest->inspection_date)->diffInDays(now());
            if ($days <= 30) {
                $terpantau++;
            } elseif ($days <= 90) {
                $perluPemeriksaan++;
            } else {
                $prioritasTinggi++;
            }
        }

        return [
            'total' => $total,
            'terpantau' => $terpantau,
            'perlu_pemeriksaan' => $perluPemeriksaan,
            'prioritas_tinggi' => $prioritasTinggi,
        ];
    }

    public function getMapData()
    {
        return Tower::with([
            'visits' => fn($q) => $q->latest()->limit(3), 
            'visits.creator', 
            'visits.images'
        ])
            ->get()
            ->map(function ($item) {
                $visits = $item->visits;
                $latestVisit = $visits->first();
                
                // Calculate Status Color
                $statusColor = 'red'; // Default: never inspected
                if ($latestVisit) {
                    $daysSinceVisit = \Carbon\Carbon::parse($latestVisit->inspection_date)->diffInDays(now());
                    
                    if ($daysSinceVisit <= 30) {
                        $statusColor = 'green';
                    } elseif ($daysSinceVisit <= 90) {
                        $statusColor = 'yellow';
                    }
                }

                return [
                    'id' => $item->id,
                    'tower_id' => $item->tower_id,
                    'lokasi' => $item->location_name,
                    'detail' => $item->location_detail,
                    'kecamatan' => $item->kecamatan,
                    'kelurahan' => $item->kelurahan,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'status_color' => $statusColor,
                    // Riwayat 3 terakhir
                    'history' => $visits->map(fn($v) => [
                        'tanggal' => \Carbon\Carbon::parse($v->inspection_date)->isoFormat('D MMMM YYYY'),
                        'visitor' => $v->creator?->name ?? 'Unknown',
                    ])->toArray(),
                    // Foto dari kunjungan terakhir (3 saja)
                    'latest_fotos' => $latestVisit ? $latestVisit->images->take(3)->map(fn($img) => \Illuminate\Support\Facades\Storage::disk('public')->url($img->image_path))->toArray() : [],
                ];
            })
            ->values();
    }
}
