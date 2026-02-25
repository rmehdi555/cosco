<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $modelLabel = 'محصول';

    protected static ?string $pluralModelLabel = 'محصولات';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'محصولات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('اطلاعات اصلی')->schema([
                        TextInput::make('name')
                            ->label('نام محصول')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('نام محصول را وارد کنید')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set) {
                                if (!empty($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('code')
                            ->label('کد محصول')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('کد یکتای محصول')
                            ->helperText('کد منحصر به فرد محصول'),

                        TextInput::make('slug')
                            ->label('نامک')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('نامک محصول')
                            ->helperText('نامک منحصر به فرد برای URL'),

                        Select::make('product_category_id')
                            ->label('دسته‌بندی')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب دسته‌بندی'),

                        Select::make('brand_id')
                            ->label('برند')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب برند'),
                    ])->columnSpan(1),

                    Section::make('قیمت و موجودی')->schema([
                        TextInput::make('price')
                            ->label('قیمت')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ریال')
                            ->required()
                            ->helperText('قیمت محصول به ریال'),

                        TextInput::make('stock')
                            ->label('موجودی')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('تعداد موجود در انبار'),
                    ])->columnSpan(1),
                ]),

                Section::make('توضیحات')->schema([
                    Textarea::make('description')
                        ->label('توضیحات محصول')
                        ->required()
                        ->rows(5)
                        ->placeholder('توضیحات کامل محصول را وارد کنید')
                        ->helperText('توضیحات کامل محصول برای نمایش به مشتریان'),
                ]),
                TinyEditor::make('body')
                ->label('توضیحات کامل محصول')
                ->columnSpanFull(),

                Section::make('آلبوم تصاویر')->schema([
                    Repeater::make('images')
                        ->label('تصاویر محصول')
                        ->relationship('images')
                        ->schema([
                            FileUpload::make('image_url')
                                ->label('تصویر')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('800')
                                ->imageResizeTargetHeight('800')
                                ->disk('public')
                                ->directory('product-images')
                                ->maxSize(2048)
                                ->required()
                                ->helperText('تصویر محصول - حداکثر 2 مگابایت'),

                            Toggle::make('is_main')
                                ->label('تصویر اصلی')
                                ->default(false)
                                ->helperText('این تصویر به عنوان تصویر اصلی محصول نمایش داده می‌شود')
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set, $get, $context) {
                                    // If this image is set as main, unset others
                                    if ($state && $context === 'create') {
                                        $set('is_main', true);
                                    }
                                }),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => 
                                        is_array($state['image_url'] ?? null)
                                            ? ($state['image_url'][0] ?? null)
                                            : ($state['image_url'] ?? null)
                                    )
                        ->addActionLabel('افزودن تصویر جدید')
                        ->cloneable()
                        ->helperText('تصاویر محصول را اضافه کنید. حداقل یک تصویر اصلی انتخاب کنید.')
                        ->columnSpanFull(),
                ]),

                Section::make('تنظیمات')->schema([
                    Grid::make(3)->schema([
                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->helperText('آیا این محصول فعال باشد؟'),

                        Toggle::make('is_featured')
                            ->label('ویژه')
                            ->default(false)
                            ->helperText('آیا این محصول ویژه باشد؟'),

                        Toggle::make('is_online_only')
                            ->label('فقط آنلاین')
                            ->default(false)
                            ->helperText('آیا این محصول فقط آنلاین فروخته می‌شود؟'),
                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شماره محصول')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('code')
                    ->label('کد محصول')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable(),

                ImageColumn::make('main_image')
                    ->label('تصویر اصلی')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        $mainImage = $record->images()->where('is_main', true)->first();
                        return $mainImage ? $mainImage->image_url : null;
                    }),

                TextColumn::make('name')
                    ->label('نام محصول')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('brand.name')
                    ->label('برند')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('price')
                    ->label('قیمت')
                    ->money('IRR')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('موجودی')
                    ->sortable()
                    ->color(fn (int $state): string => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->badge(),

                TextColumn::make('images_count')
                    ->label('تعداد تصاویر')
                    ->counts('images')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('ویژه')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_online_only')
                    ->label('فقط آنلاین')
                    ->boolean()
                    ->trueIcon('heroicon-o-computer-desktop')
                    ->falseIcon('heroicon-o-building-storefront')
                    ->trueColor('info')
                    ->falseColor('gray')
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
                SelectFilter::make('category')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable(),

                SelectFilter::make('brand')
                    ->label('برند')
                    ->relationship('brand', 'name')
                    ->searchable(),

                TernaryFilter::make('is_active')
                    ->label('وضعیت فعال')
                    ->placeholder('همه محصولات')
                    ->trueLabel('فقط محصولات فعال')
                    ->falseLabel('فقط محصولات غیرفعال'),

                TernaryFilter::make('is_featured')
                    ->label('محصولات ویژه')
                    ->placeholder('همه محصولات')
                    ->trueLabel('فقط محصولات ویژه')
                    ->falseLabel('محصولات عادی'),

                TernaryFilter::make('is_online_only')
                    ->label('فقط آنلاین')
                    ->placeholder('همه محصولات')
                    ->trueLabel('فقط محصولات آنلاین')
                    ->falseLabel('محصولات فیزیکی'),

                Filter::make('price')
                    ->form([
                        TextInput::make('min_price')
                            ->label('حداقل قیمت')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('max_price')
                            ->label('حداکثر قیمت')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                Filter::make('stock')
                    ->form([
                        TextInput::make('min_stock')
                            ->label('حداقل موجودی')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('max_stock')
                            ->label('حداکثر موجودی')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_stock'],
                                fn (Builder $query, $stock): Builder => $query->where('stock', '>=', $stock),
                            )
                            ->when(
                                $data['max_stock'],
                                fn (Builder $query, $stock): Builder => $query->where('stock', '<=', $stock),
                            );
                    }),

                Filter::make('out_of_stock')
                    ->label('ناموجود')
                    ->query(fn (Builder $query): Builder => $query->where('stock', 0)),

                Filter::make('low_stock')
                    ->label('موجودی کم')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '>', 0)->where('stock', '<=', 10)),

                Filter::make('has_images')
                    ->label('دارای تصویر')
                    ->query(fn (Builder $query): Builder => $query->has('images')),

                Filter::make('no_images')
                    ->label('بدون تصویر')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('images')),

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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 