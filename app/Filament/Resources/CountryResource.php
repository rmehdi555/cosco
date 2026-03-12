<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-flag';
    }

    protected static ?string $modelLabel = 'کشور';

    protected static ?string $pluralModelLabel = 'کشورها';

    protected static ?string $slug = 'countries';

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت جغرافیایی';
    }

    protected static ?int $navigationSort = 1;

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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('provinces'))
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

                TextColumn::make('provinces_count')
                    ->label('تعداد استان‌ها')
                    ->counts('provinces')
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
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
} 