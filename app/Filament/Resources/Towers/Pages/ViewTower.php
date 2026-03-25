<?php

namespace App\Filament\Resources\Towers\Pages;

use App\Filament\Resources\Towers\TowerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTower extends ViewRecord
{
    protected static string $resource = TowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
