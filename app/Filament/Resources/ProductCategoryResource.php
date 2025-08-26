<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'دسته‌بندی محصولات';

    protected static ?string $pluralModelLabel = 'دسته‌بندی محصولات';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'محصولات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Grid::make(1)->schema([
                        Section::make('اطلاعات اصلی')->schema([
                            TextInput::make('name')
                                ->label('نام')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                            TextInput::make('slug')
                                ->label('اسلاگ')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            Select::make('parent_id')
                                ->label('دسته‌بندی والد')
                                ->relationship('parent', 'name')
                                ->searchable()
                                ->placeholder('انتخاب کنید (اختیاری)')
                                ->nullable(),

                            Textarea::make('description')
                                ->label('توضیحات')
                                ->required()
                                ->maxLength(1000)
                                ->rows(4),
                        ]),

                        Section::make('تنظیمات')->schema([
                            Toggle::make('is_active')
                                ->label('فعال')
                                ->default(true),
                        ]),
                    ])->columnSpan(2),

                    Section::make('تصویر')->schema([
                        FileUpload::make('image_url')
                            ->label('تصویر دسته‌بندی')
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->disk('public')
                            ->directory('product-categories')
                            ->maxSize(2048)
                            ->helperText('حداکثر اندازه: 2MB'),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('تصویر')
                    ->circular()
                    ->size(50),

                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('اسلاگ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('دسته‌بندی والد')
                    ->sortable()
                    ->placeholder('بدون والد'),

                TextColumn::make('children_count')
                    ->label('تعداد زیرمجموعه')
                    ->counts('children')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('parent_id')
                    ->label('فقط دسته‌بندی‌های اصلی')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),

                Filter::make('is_active')
                    ->label('فقط فعال')
                    ->toggle(),

                Filter::make('name')
                    ->form([
                        TextInput::make('name')
                            ->label('نام'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['name'],
                        fn (Builder $query, $data): Builder => $query->where('name', 'like', '%' . $data . '%'),
                    )),
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
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 