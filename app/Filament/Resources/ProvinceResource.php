<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Models\Province;
use App\Models\Country;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-map';
    }

    protected static ?string $modelLabel = 'استان';

    protected static ?string $pluralModelLabel = 'استان‌ها';

    protected static ?string $slug = 'provinces';

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت جغرافیایی';
    }

    protected static ?int $navigationSort = 2;

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

            Select::make('country_id')
                ->label('کشور')
                ->options(Country::query()->pluck('title_fa', 'id'))
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('country')->withCount('cities'))
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

                TextColumn::make('country.title_fa')
                    ->label('کشور')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cities_count')
                    ->label('تعداد شهرها')
                    ->counts('cities')
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
                SelectFilter::make('country_id')
                    ->label('کشور')
                    ->options(Country::query()->pluck('title_fa', 'id'))
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
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
} 