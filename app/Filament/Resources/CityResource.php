<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\Province;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office';
    }

    protected static ?string $modelLabel = 'شهر';

    protected static ?string $pluralModelLabel = 'شهرها';

    protected static ?string $slug = 'cities';

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت جغرافیایی';
    }

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title_fa')
                ->label('نام فارسی')
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('title_en')
                ->label('نام انگلیسی')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Select::make('province_id')
                ->label('استان')
                ->options(Province::query()->pluck('title_fa', 'id'))
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['province.country']))
            ->columns([
                TextColumn::make('id')
                    ->label('آی دی')
                    ->sortable(),

                TextColumn::make('title_fa')
                    ->label('نام فارسی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title_en')
                    ->label('نام انگلیسی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('province.title_fa')
                    ->label('استان')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('province.country.title_fa')
                    ->label('کشور')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('province_id')
                    ->label('استان')
                    ->options(Province::query()->pluck('title_fa', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
} 