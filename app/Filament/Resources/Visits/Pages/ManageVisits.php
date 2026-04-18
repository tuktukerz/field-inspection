<?php

namespace App\Filament\Resources\Visits\Pages;

use App\Filament\Resources\Visits\VisitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageVisits extends ManageRecords
{
    protected static string $resource = VisitResource::class;

    public function hasResourceBreadcrumbs(): bool
    {
        return true;
    }

    public function getBreadcrumbs(): array
    {
        return [
            ...$this->getResourceBreadcrumbs(),
            'Daftar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Riwayat Visit'),
        ];
    }
}
