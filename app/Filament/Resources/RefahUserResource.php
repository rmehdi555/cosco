<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefahUserResource\Pages;
use App\Models\RefahUser;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\RefahCart;
use App\Models\RefahOrganization;
use App\Filament\ExcelExport\RefahUserExport;
use Hekmatinasser\Verta\Verta;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RefahUserResource extends Resource
{
    protected static ?string $model = RefahUser::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected static ?string $modelLabel = 'کاربر رفاه';

    protected static ?string $pluralModelLabel = 'کاربران رفاه';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت رفاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات شخصی')
                    ->description('اطلاعات شخصی کاربر')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('first_name')
                                ->label('نام')
                                ->maxLength(255)
                                ->placeholder('نام را وارد کنید'),

                            TextInput::make('last_name')
                                ->label('نام خانوادگی')
                                ->maxLength(255)
                                ->placeholder('نام خانوادگی را وارد کنید'),

                            TextInput::make('national_code')
                                ->label('کد ملی')
                                ->maxLength(10)
                                ->unique(ignoreRecord: true)
                                ->placeholder('کد ملی 10 رقمی'),

                            TextInput::make('code')
                                ->label('کد پیگیری')
                                ->maxLength(50)
                                ->disabled()
                                ->dehydrated(false)
                                ->placeholder('کد به صورت خودکار تولید می‌شود')
                                ->helperText('کد 6 رقمی منحصر به فرد که به صورت خودکار تولید می‌شود')
                                ->visible(fn ($record) => $record !== null),

                            DatePicker::make('birth_date')
                                ->label('تاریخ تولد')
                                ->native(false)
                                ->displayFormat('Y/m/d')
                                ->helperText('تاریخ به صورت شمسی نمایش داده می‌شود'),

                            Select::make('gender')
                                ->label('جنسیت')
                                ->options([
                                    'male' => 'مرد',
                                    'female' => 'زن',
                                ])
                                ->default('male')
                                ->native(false),

                            TextInput::make('number_of_family_members')
                                ->label('تعداد اعضای خانواده')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),
                    ]),

                Section::make('اطلاعات تماس')
                    ->description('شماره تماس و آدرس')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('cell_phone')
                                ->label('شماره موبایل')
                                ->tel()
                                ->maxLength(11)
                                ->placeholder('09xxxxxxxxx'),

                            TextInput::make('phone')
                                ->label('تلفن ثابت')
                                ->tel()
                                ->placeholder('021xxxxxxxx'),

                            Select::make('country_id')
                                ->label('کشور')
                                ->options(Country::pluck('title_fa', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (callable $set) => $set('province_id', null)),

                            Select::make('province_id')
                                ->label('استان')
                                ->options(fn (Get $get): Collection => Province::query()
                                    ->where('country_id', $get('country_id'))
                                    ->pluck('title_fa', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),

                            Select::make('city_id')
                                ->label('شهر')
                                ->options(fn (Get $get): Collection => City::query()
                                    ->where('province_id', $get('province_id'))
                                    ->pluck('title_fa', 'id'))
                                ->searchable()
                                ->preload(),

                            TextInput::make('postal_code')
                                ->label('کد پستی')
                                ->maxLength(10)
                                ->placeholder('1234567890'),
                        ]),

                        Textarea::make('address')
                            ->label('آدرس کامل')
                            ->maxLength(500)
                            ->placeholder('آدرس کامل خود را وارد کنید')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('اطلاعات شغلی و مالی')
                    ->description('شغل و درآمد')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('job')
                                ->label('شغل')
                                ->maxLength(255)
                                ->placeholder('شغل خود را وارد کنید'),

                            TextInput::make('income')
                                ->label('درآمد (ریال)')
                                ->numeric()
                                ->prefix('ریال')
                                ->placeholder('0'),
                        ]),
                    ]),

                Section::make('اطلاعات بسته رفاهی')
                    ->description('انتخاب بسته و روش دریافت')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('refah_organization_id')
                                ->label('سازمان رفاه')
                                ->options(RefahOrganization::where('is_active', true)->pluck('title', 'id'))
                                ->searchable()
                                ->preload(),

                            Select::make('refah_cart_id')
                                ->label('بسته رفاهی')
                                ->options(RefahCart::where('is_active', true)->pluck('title', 'id'))
                                ->searchable()
                                ->preload(),

                            Select::make('how_to_receive')
                                ->label('نحوه دریافت')
                                ->options([
                                    'in_person' => 'حضوری',
                                    'mail_to_address' => 'ارسال به آدرس',
                                ])
                                ->default('in_person')
                                ->native(false),

                            Select::make('payment_method')
                                ->label('روش پرداخت')
                                ->options([
                                    'cash' => 'نقدی',
                                    'card' => 'کارت',
                                    'online' => 'آنلاین',
                                    'installment' => 'اقساطی',
                                ])
                                ->default('cash')
                                ->native(false),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('نام و نام خانوادگی')
                    ->state(fn (RefahUser $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('national_code')
                    ->label('کد ملی')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('کد ملی کپی شد'),

                TextColumn::make('code')
                    ->label('کد پیگیری')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('کد پیگیری کپی شد')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('cell_phone')
                    ->label('موبایل')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('شماره موبایل کپی شد'),

                TextColumn::make('gender')
                    ->label('جنسیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'مرد',
                        'female' => 'زن',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('birth_date')
                    ->label('تاریخ تولد')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return Verta::instance($state)->format('Y/n/j');
                        }
                        return '-';
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('province.title_fa')
                    ->label('استان')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('city.title_fa')
                    ->label('شهر')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('address')
                    ->label('آدرس')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('job')
                    ->label('شغل')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('income')
                    ->label('درآمد')
                    ->money('IRR')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('refahOrganization.title')
                    ->label('سازمان رفاه')
                    ->limit(30)
                    ->badge()
                    ->color('info'),

                TextColumn::make('refahCart.title')
                    ->label('بسته رفاهی')
                    ->limit(30)
                    ->badge()
                    ->color('success'),

                TextColumn::make('how_to_receive')
                    ->label('نحوه دریافت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_person' => 'حضوری',
                        'mail_to_address' => 'ارسال به آدرس',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in_person' => 'info',
                        'mail_to_address' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->label('روش پرداخت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'نقدی',
                        'card' => 'کارت',
                        'online' => 'آنلاین',
                        'installment' => 'اقساطی',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'card' => 'info',
                        'online' => 'warning',
                        'installment' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت‌نام')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return Verta::instance($state)->format('Y/n/j H:i');
                        }
                        return '-';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label('جنسیت')
                    ->options([
                        'male' => 'مرد',
                        'female' => 'زن',
                    ])
                    ->native(false),

                SelectFilter::make('province_id')
                    ->label('استان')
                    ->relationship('province', 'title_fa')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('refah_organization_id')
                    ->label('سازمان رفاه')
                    ->relationship('refahOrganization', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('refah_cart_id')
                    ->label('بسته رفاهی')
                    ->relationship('refahCart', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('how_to_receive')
                    ->label('نحوه دریافت')
                    ->options([
                        'in_person' => 'حضوری',
                        'mail_to_address' => 'ارسال به آدرس',
                    ])
                    ->native(false),

                SelectFilter::make('payment_method')
                    ->label('روش پرداخت')
                    ->options([
                        'cash' => 'نقدی',
                        'card' => 'کارت',
                        'online' => 'آنلاین',
                        'installment' => 'اقساطی',
                    ])
                    ->native(false),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('خروجی Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return RefahUserExport::exportAll();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Action::make('export_selected')
                        ->label('خروجی انتخاب شده‌ها')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            return RefahUserExport::exportSelected($records);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefahUsers::route('/'),
            'create' => Pages\CreateRefahUser::route('/create'),
            'view' => Pages\ViewRefahUser::route('/{record}'),
            'edit' => Pages\EditRefahUser::route('/{record}/edit'),
        ];
    }
}
