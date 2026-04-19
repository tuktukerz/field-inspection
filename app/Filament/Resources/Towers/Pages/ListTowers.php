<?php

namespace App\Filament\Resources\Towers\Pages;

use App\Filament\Resources\Towers\TowerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTowers extends ListRecords
{
    protected static string $resource = TowerResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            'SIMTEL',
            'Daftar Menara',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Menara'),
        ];
    }
}
