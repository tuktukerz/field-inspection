<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\FieldInspection;

class MapDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

//    protected static string|\BackedEnum|null $navigationLabel = 'Peta Persebaran';

    public function getTitle(): string
    {
        return 'Peta Persebaran Data';
    }

    protected string $view = 'filament.pages.map-dashboard';

    public function getMapData()
    {
        return FieldInspection::with('images')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'tanggal' => \Carbon\Carbon::parse($item->inspection_date)->isoFormat('D MMMM YYYY'),
                'lokasi' => $item->location_name,
                'detail' => $item->location_detail,
                'kecamatan' => $item->kecamatan,
                'kelurahan' => $item->kelurahan,
                'letak' => strtoupper($item->location_type),
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'foto' => $item->images->first()?->image_path,
            ])
            ->values();
    }
}
