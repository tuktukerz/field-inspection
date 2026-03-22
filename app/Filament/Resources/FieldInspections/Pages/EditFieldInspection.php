<?php

namespace App\Filament\Resources\FieldInspections\Pages;

use App\Filament\Resources\FieldInspections\FieldInspectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFieldInspection extends EditRecord
{
    protected static string $resource = FieldInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
