<?php

namespace App\Filament\Resources\FieldInspections\Pages;

use App\Filament\Resources\FieldInspections\FieldInspectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFieldInspection extends ViewRecord
{
    protected static string $resource = FieldInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
