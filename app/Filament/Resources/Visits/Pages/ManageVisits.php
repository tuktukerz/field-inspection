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
            'SIMTEL',
            'Inspeksi',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Inspeksi')
                ->modalHeading('Tambah Data Inspeksi Menara Telekomunikasi')
                ->modalSubmitActionLabel('Simpan')
                ->createAnother(false),
        ];
    }
}
