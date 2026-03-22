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
                'nama' => $item->location_name,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'kelurahan' => $item->kelurahan,
                'kecamatan' => $item->kecamatan,
                'foto' => $item->images->first()?->image_path,
            ])
            ->values();
    }
}
