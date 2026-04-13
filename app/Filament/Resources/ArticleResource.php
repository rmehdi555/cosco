<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Forms\Components\TinyEditor;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-pencil-square';
    }

    protected static ?string $modelLabel = 'مقاله';

    protected static ?string $pluralModelLabel = 'مقالات';

    protected static ?int $navigationSort = 1;

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
                            TextInput::make('title')
                                ->label('عنوان')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('عنوان مقاله را وارد کنید')
                                ->columnSpanFull()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $state, callable $set) {
                                    if (! empty($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make('slug')
                                ->label('نامک')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->placeholder('نامک برای آدرس صفحه')
                                ->helperText('نامک منحصر به فرد برای URL'),

                            Select::make('category_id')
                                ->relationship('category', 'name', function ($query) {
                                    return $query->whereNotNull('name')->where('name', '!=', '');
                                })
                                ->label('دسته‌بندی')
                                ->required()
                                ->searchable()
                                ->placeholder('انتخاب دسته‌بندی'),

                            Select::make('user_id')
                                ->relationship('user', 'first_name', function ($query) {
                                    return $query->whereNotNull('first_name')->where('first_name', '!=', '');
                                })
                                ->label('نویسنده')
                                ->required()
                                ->searchable()
                                ->placeholder('انتخاب کاربر'),
                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('خلاصه و متن')
                    ->schema([
                        Textarea::make('excerpt')
                            ->label('خلاصه')
                            ->required()
                            ->rows(5)
                            ->maxLength(65535)
                            ->placeholder('خلاصه کوتاه برای لیست و پیش‌نمایش')
                            ->helperText('خلاصه‌ای کوتاه از مقاله برای نمایش در لیست و کارت‌ها')
                            ->columnSpanFull(),

                        TinyEditor::make('body')
                            ->label('متن کامل مقاله')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('uploads')
                            ->required()
                            ->resize('vertical')
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

                Section::make('تنظیمات')
                    ->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('image_url')
                                ->label('تصویر شاخص')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('articles')
                                ->maxSize(2048)
                                ->nullable()
                                ->helperText('تصویر شاخص مقاله — حداکثر ۲ مگابایت'),

                            TextInput::make('view_count')
                                ->label('تعداد بازدید')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->helperText('برای نمایش آماری (قابل ویرایش دستی)'),

                            Toggle::make('is_show')
                                ->label('نمایش در سایت')
                                ->default(true)
                                ->helperText('آیا مقاله برای بازدیدکنندگان قابل مشاهده باشد؟'),

                            Toggle::make('is_future')
                                ->label('انتشار آینده')
                                ->default(false)
                                ->helperText('برای زمان‌بندی انتشار در آینده'),
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
                    ->getStateUsing(fn (Article $record): ?string => $record->image_url
                        ? asset('storage/'.$record->image_url)
                        : null),

                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('link_view_article')
                    ->label('لینک سایت')
                    ->url(fn (Article $article) => $article->slug ? config('app.front_url').'/articles/'.$article->slug : null)
                    ->getStateUsing(fn (Article $article) => $article->slug ? 'مشاهده' : '—')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary'),

                TextColumn::make('category.name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('user.first_name')
                    ->label('نویسنده')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('view_count')
                    ->label('بازدید')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_show')
                    ->label('نمایش')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make('is_future')
                    ->label('آینده')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('warning')
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
                    ->relationship('category', 'name', function ($query) {
                        return $query->whereNotNull('name')->where('name', '!=', '');
                    })
                    ->searchable(),

                SelectFilter::make('user')
                    ->label('نویسنده')
                    ->relationship('user', 'first_name', function ($query) {
                        return $query->whereNotNull('first_name')->where('first_name', '!=', '');
                    })
                    ->searchable(),

                Filter::make('title')
                    ->form([
                        TextInput::make('title')->label('عنوان'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['title'] ?? null)) {
                            $query->where('articles.title', 'like', '%'.$data['title'].'%');
                        }

                        return $query;
                    }),

                Filter::make('slug')
                    ->form([
                        TextInput::make('slug')->label('نامک'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['slug'] ?? null)) {
                            $query->where('articles.slug', 'like', '%'.$data['slug'].'%');
                        }

                        return $query;
                    }),

                TernaryFilter::make('is_show')
                    ->label('وضعیت نمایش')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('مخفی'),

                TernaryFilter::make('is_future')
                    ->label('انتشار آینده')
                    ->placeholder('همه')
                    ->trueLabel('بله')
                    ->falseLabel('خیر'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->isAdmin();
    }
}
