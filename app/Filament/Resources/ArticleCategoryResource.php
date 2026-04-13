<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-inbox-stack';
    }

    protected static ?string $modelLabel = 'دسته‌بندی مقالات';

    protected static ?string $pluralModelLabel = 'دسته‌بندی مقالات';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'محتوا';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات اصلی')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('نام')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('نام دسته را وارد کنید')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $state, callable $set) {
                                    if (! empty($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make('slug')
                                ->label('نامک')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->placeholder('نامک برای آدرس صفحه')
                                ->helperText('نامک منحصر به فرد برای URL'),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('توضیحات')
                    ->schema([
                        Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(65535)
                            ->nullable()
                            ->rows(5)
                            ->placeholder('توضیح کوتاه دربارهٔ این دسته')
                            ->helperText('اختیاری؛ برای معرفی دسته در صفحات لیست')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('سئو')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('seo_title')
                                ->label('تایتل صفحه')
                                ->maxLength(255)
                                ->nullable()
                                ->placeholder('عنوان متا (اختیاری)'),

                            TextInput::make('seo_canonical')
                                ->label('آدرس canonical')
                                ->maxLength(255)
                                ->nullable()
                                ->placeholder('https://...'),

                            Textarea::make('seo_description')
                                ->label('توضیحات متا')
                                ->maxLength(65535)
                                ->nullable()
                                ->rows(3)
                                ->columnSpanFull(),

                            Toggle::make('seo_follow')
                                ->label('follow')
                                ->default(true)
                                ->helperText('اجازه دنبال کردن لینک‌ها توسط موتور جستجو'),

                            Toggle::make('seo_index')
                                ->label('index')
                                ->default(true)
                                ->helperText('اجازه ایندکس شدن صفحه'),
                        ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make('تصویر و نمایش')
                    ->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('image_url')
                                ->label('تصویر دسته')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('article-categories')
                                ->maxSize(2048)
                                ->nullable()
                                ->helperText('حداکثر ۲ مگابایت — اختیاری'),

                            Toggle::make('is_show')
                                ->label('نمایش در سایت')
                                ->default(true)
                                ->helperText('آیا این دسته برای بازدیدکنندگان قابل مشاهده باشد؟'),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                ImageColumn::make('image_url')
                    ->label('تصویر')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(fn (ArticleCategory $record): ?string => $record->image_url
                        ? asset('storage/'.$record->image_url)
                        : null),

                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                TextColumn::make('slug')
                    ->label('نامک')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->limit(30),

                IconColumn::make('is_show')
                    ->label('نمایش')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
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
                Filter::make('name')
                    ->form([
                        TextInput::make('name')->label('نام'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['name'] ?? null)) {
                            $query->where('name', 'like', '%'.$data['name'].'%');
                        }

                        return $query;
                    }),

                TernaryFilter::make('is_show')
                    ->label('وضعیت نمایش')
                    ->placeholder('همه')
                    ->trueLabel('نمایش')
                    ->falseLabel('مخفی'),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }
}
