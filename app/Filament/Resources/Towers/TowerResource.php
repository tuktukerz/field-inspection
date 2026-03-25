<?php

namespace App\Filament\Resources\Towers;

use App\Filament\Resources\Towers\Pages\CreateTower;
use App\Filament\Resources\Towers\Pages\EditTower;
use App\Filament\Resources\Towers\Pages\ListTowers;
use App\Filament\Resources\Towers\Pages\ViewTower;
use App\Models\Tower;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Resources\Towers\Schemas\TowerForm;
use App\Filament\Resources\Towers\Tables\TowersTable;
use App\Filament\Resources\Towers\RelationManagers\VisitsRelationManager;

class TowerResource extends Resource
{
    protected static ?string $model = Tower::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'location_name';

    public static function form(Schema $schema): Schema
    {
        return TowerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TowersTable::configure($table)
            ->actions(TowersTable::actions())
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTowers::route('/'),
            'create' => CreateTower::route('/create'),
            'view' => ViewTower::route('/{record}'),
            'edit' => EditTower::route('/{record}/edit'),
        ];
    }
}
