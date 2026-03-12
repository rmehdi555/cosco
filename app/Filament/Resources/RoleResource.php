<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shield-check';
    }

    protected static ?string $modelLabel = 'نقش';

    protected static ?string $pluralModelLabel = 'لیست نقش ها';

    protected static ?string $slug = 'roles';

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت کاربران';
    }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(1)->schema([
                TextInput::make('name')->label('نام')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('permissions')
                    ->label('دسترسی ها')
                    ->color('danger')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Select::make('permission')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->default(fn(Role $record) => $record->permissions->pluck('id')->toArray())
                            ->label('دسترسی ها')
                            ->preload()
                    ])
                    ->action(fn(Role $record, array $data) => $record),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
}
