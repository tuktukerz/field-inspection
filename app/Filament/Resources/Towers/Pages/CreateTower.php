<?php

namespace App\Filament\Resources\Towers\Pages;

use App\Filament\Resources\Towers\TowerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTower extends CreateRecord
{
    protected static string $resource = TowerResource::class;

    public function getTitle(): string
    {
        return 'Tambah Data Menara Telekomunikasi';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Data';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'SIMTEL',
            TowerResource::getUrl() => 'Daftar Menara',
            'Tambah Data',
        ];
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }
}
