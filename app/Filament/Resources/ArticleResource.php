<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\User;
use App\Models\ArticleCategory;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;

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
                Grid::make(3)->schema([

                    Grid::make(1)->schema([
                        Grid::make(1)->schema([
                            TextInput::make('title')->label('عنوان')->columnSpan(2)->required(),
                        ]),

                        Section::make()->schema([
                            Textarea::make('excerpt')->label('خلاصه')->maxLength(65535)->required(),
                            TinyEditor::make('body')->label('متن')->fileAttachmentsDisk('public')->fileAttachmentsVisibility('public')->fileAttachmentsDirectory('uploads')->required()->resize('vertical')->columnSpanFull(),
                        ]),

                        Section::make('سئو')->schema([
                            TextInput::make('seo_title')->label('تایتل صفحه')->maxLength(255)->nullable(),
                            Textarea::make('seo_description')->label('توضیحات صفحه')->maxLength(65535)->nullable(),
                            Toggle::make('seo_follow')->label('follow')->default(true),
                            Toggle::make('seo_index')->label('index')->default(true),
                            TextInput::make('seo_canonical')->label('canonical')->nullable(),
                        ])->collapsed(),

                    ])->columnSpan(2),

                    Section::make()->schema([
                        TextInput::make('slug')->label('اسلاگ')->unique(ignoreRecord: true)->maxLength(255)->required(),
                        Select::make('category_id')
                            ->relationship('category', 'name', function ($query) {
                                return $query->whereNotNull('name')->where('name', '!=', '');
                            })
                            ->label('دسته بندی')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب دسته بندی'),
                        Select::make('user_id')
                            ->relationship('user', 'first_name', function ($query) {
                                return $query->whereNotNull('first_name')->where('first_name', '!=', '');
                            })
                            ->label('کاربر')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب کاربر'),
                        FileUpload::make('image_url')->image()->label('تصویر')->imageEditor()->nullable(),
                        TextInput::make('view_count')->label('تعداد بازدید')->numeric()->default(0),
                        Toggle::make('is_show')->label('وضعیت نمایش')->default(true),
                        Toggle::make('is_future')->label('انتشار آینده')->default(false),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('view_count')->label('تعداد بازدید'),
                TextColumn::make('link_view_article')->label('نمایش در سایت ')
                    ->url(fn(Article $article) => $article->slug ? config('app.front_url') . "/articles/" . $article->slug : null)
                    ->getStateUsing(fn(Article $article) => $article->slug ?: 'بدون اسلاگ')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->color('primary'),
                TextColumn::make('category.name')
                    ->label('دسته بندی')
                    ->placeholder('بدون دسته بندی'),
                TextColumn::make('user.first_name')
                    ->label('کاربر')
                    ->placeholder('کاربر نامشخص'),
                IconColumn::make('is_show')->label('وضعیت نمایش')->boolean(),
                IconColumn::make('is_future')->label('انتشار آینده')->boolean(),
                TextColumn::make('created_at')->label('ایجاد در')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('دسته بندی')
                    ->relationship('category', 'name', function ($query) {
                        return $query->whereNotNull('name')->where('name', '!=', '');
                    }),
                SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'first_name', function ($query) {
                        return $query->whereNotNull('first_name')->where('first_name', '!=', '');
                    }),
                Filter::make('title')->form([
                    TextInput::make('title')->label('عنوان'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['title'],
                    fn(Builder $query, $data): Builder => $query->where('articles.title', 'like', '%' . $data . '%'),
                )),
                Filter::make('slug')->form([
                    TextInput::make('slug')->label('اسلاگ'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['slug'],
                    fn(Builder $query, $data): Builder => $query->where('articles.slug', 'like', '%' . $data . '%'),
                )),
                Filter::make('is_show')->label('وضعیت نمایش')->toggle(),
                Filter::make('is_future')->label('انتشار آینده')->toggle(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
            ])->defaultSort('created_at', 'desc');
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

