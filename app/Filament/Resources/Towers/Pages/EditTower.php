<?php

namespace App\Filament\Resources\Towers\Pages;

use App\Filament\Resources\Towers\TowerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTower extends EditRecord
{
    protected static string $resource = TowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
