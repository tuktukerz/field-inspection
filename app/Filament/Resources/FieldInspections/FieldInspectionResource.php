<?php

namespace App\Filament\Resources\FieldInspections;

use App\Filament\Resources\FieldInspections\Pages\CreateFieldInspection;
use App\Filament\Resources\FieldInspections\Pages\EditFieldInspection;
use App\Filament\Resources\FieldInspections\Pages\ListFieldInspections;
use App\Filament\Resources\FieldInspections\Schemas\FieldInspectionForm;
use App\Filament\Resources\FieldInspections\Tables\FieldInspectionsTable;
use App\Models\FieldInspection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;



class FieldInspectionResource extends Resource
{
    protected static ?string $model = FieldInspection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'document_number';

    protected static ?string $navigationLabel = 'Data Inspeksi Lapangan';
    protected static ?string $modelLabel = 'Inspeksi Lapangan';
    protected static ?string $pluralModelLabel = 'Inspeksi Lapangan';

    public static function form(Schema $schema): Schema
    {
        return FieldInspectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldInspectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        // ✅ Super admin → lihat semua
        if ($user->role === 'super_admin') {
            return $query;
        }

        // ✅ Admin → hanya miliknya
        return $query->where('created_by', $user->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFieldInspections::route('/'),
            'create' => CreateFieldInspection::route('/create'),
            'view' => Pages\ViewFieldInspection::route('/{record}'),
            'edit' => EditFieldInspection::route('/{record}/edit'),
        ];
    }
}
