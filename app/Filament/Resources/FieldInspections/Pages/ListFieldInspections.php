<?php

namespace App\Filament\Resources\FieldInspections\Pages;

use App\Filament\Resources\FieldInspections\FieldInspectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFieldInspections extends ListRecords
{
    protected static string $resource = FieldInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
