<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Tower;

class MapDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Peta Persebaran Data';
    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return 'Peta Persebaran Data';
    }

    protected string $view = 'filament.pages.map-dashboard';

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
