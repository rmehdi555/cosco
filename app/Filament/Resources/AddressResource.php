<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Models\Address;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** @see \Filament\Resources\Resource */
class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-map-pin';
    }

    protected static ?string $modelLabel = 'آدرس';

    protected static ?string $pluralModelLabel = 'آدرس‌ها';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت کاربران';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Grid::make(1)->schema([
                        Section::make('اطلاعات کاربر')->schema([
                            Select::make('user_id')
                                ->relationship('user', 'first_name')
                                ->label('کاربر')
                                ->required()
                                ->searchable(),
                        ]),

                        Section::make('اطلاعات آدرس')->schema([
                            Select::make('country_id')
                                ->label('کشور')
                                ->options(Country::pluck('title_fa', 'id'))
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('province_id', null))
                                ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                            Select::make('province_id')
                                ->label('استان')
                                ->options(function (Get $get) {
                                    $countryId = $get('country_id');
                                    if (!$countryId) return [];
                                    return Province::where('country_id', $countryId)->pluck('title_fa', 'id');
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                            Select::make('city_id')
                                ->label('شهر')
                                ->options(function (Get $get) {
                                    $provinceId = $get('province_id');
                                    if (!$provinceId) return [];
                                    return City::where('province_id', $provinceId)->pluck('title_fa', 'id');
                                })
                                ->required(),

                            TextInput::make('postal_code')
                                ->label('کد پستی')
                                ->required()
                                ->maxLength(10),

                            TextInput::make('plaque')
                                ->label('پلاک')
                                ->required()
                                ->maxLength(50),

                            Textarea::make('address')
                                ->label('آدرس کامل')
                                ->required()
                                ->maxLength(500)
                                ->rows(3),

                            TextInput::make('phone')
                                ->label('تلفن')
                                ->required()
                                ->tel()
                                ->maxLength(20),
                        ]),

                        Section::make('تنظیمات')->schema([
                            Toggle::make('is_default')
                                ->label('آدرس پیش‌فرض')
                                ->default(false),

                            Toggle::make('is_active')
                                ->label('فعال')
                                ->default(true),
                        ]),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.title_fa')
                    ->label('کشور')
                    ->sortable(),

                TextColumn::make('province.title_fa')
                    ->label('استان')
                    ->sortable(),

                TextColumn::make('city.title_fa')
                    ->label('شهر')
                    ->sortable(),

                TextColumn::make('postal_code')
                    ->label('کد پستی')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('تلفن')
                    ->searchable(),

                IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'first_name'),

                SelectFilter::make('country')
                    ->label('کشور')
                    ->relationship('country', 'title_fa'),

                SelectFilter::make('province')
                    ->label('استان')
                    ->relationship('province', 'title_fa'),

                SelectFilter::make('city')
                    ->label('شهر')
                    ->relationship('city', 'title_fa'),

                Filter::make('is_default')
                    ->label('آدرس پیش‌فرض')
                    ->toggle(),

                Filter::make('is_active')
                    ->label('فقط فعال')
                    ->toggle(),

                Filter::make('postal_code')
                    ->form([
                        TextInput::make('postal_code')
                            ->label('کد پستی'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['postal_code'],
                        fn (Builder $query, $data): Builder => $query->where('postal_code', 'like', '%' . $data . '%'),
                    )),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 