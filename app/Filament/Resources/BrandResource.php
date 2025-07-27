<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'برند';

    protected static ?string $pluralModelLabel = 'برندها';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'محصولات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات برند')->schema([
                    TextInput::make('name')
                        ->label('نام برند')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('نام برند را وارد کنید')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, callable $set) {
                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->label('نامک')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->placeholder('نامک برند')
                        ->helperText('نامک منحصر به فرد برای URL'),

                    FileUpload::make('image_url')
                        ->label('لوگو برند')
                        ->image()
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('300')
                        ->imageResizeTargetHeight('300')
                        ->disk('public')
                        ->directory('brands')
                        ->maxSize(2048)
                        ->required()
                        ->helperText('لوگو برند (حداکثر 2MB)'),

                    Toggle::make('is_active')
                        ->label('فعال')
                        ->default(true)
                        ->helperText('آیا این برند فعال باشد؟'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('لوگو')
                    ->circular()
                    ->size(50),

                TextColumn::make('name')
                    ->label('نام برند')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('نامک')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('وضعیت فعال')
                    ->placeholder('همه برندها')
                    ->trueLabel('فقط برندهای فعال')
                    ->falseLabel('فقط برندهای غیرفعال'),

                Filter::make('inactive_brands')
                    ->label('فقط برندهای غیرفعال')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false)),

                Filter::make('name')
                    ->form([
                        TextInput::make('name')
                            ->label('جستجو در نام')
                            ->placeholder('نام برند را وارد کنید'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name): Builder => $query->where('name', 'like', "%{$name}%"),
                            );
                    }),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('از تاریخ'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('today')
                    ->label('امروز')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),

                Filter::make('this_week')
                    ->label('این هفته')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),

                Filter::make('this_month')
                    ->label('این ماه')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'view' => Pages\ViewBrand::route('/{record}'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 