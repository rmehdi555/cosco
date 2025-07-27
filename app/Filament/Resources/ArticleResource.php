<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'مقاله';

    protected static ?string $pluralModelLabel = 'مقالات';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'محتوا';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([

                    Grid::make(1)->schema([
                        Grid::make(1)->schema([
                            TextInput::make('title')->label('عنوان نمایش دهنده گوگل')->columnSpan(2)->required(),
                        ]),

                        Section::make()->schema([
                            Textarea::make('description')->label('خلاصه')->maxLength(65535)->required(),
                            TinyEditor::make('body')->label('متن')->fileAttachmentsDisk('public')->fileAttachmentsVisibility('public')->fileAttachmentsDirectory('uploads')->required()->maxHeight(500),
                        ]),

                        Section::make('سئو')->schema([
                            TextInput::make('seo_title')->label('تایتل صفحه')->maxLength(255),
                            Textarea::make('seo_description')->label('توضیحات صفحه')->maxLength(65535),
                            Toggle::make('seo_follow')->label('follow'),
                            Toggle::make('seo_index')->label('index'),
                            TextInput::make('seo_canonical')->label('canonical'),
                            TextInput::make('seo_keyword')->label('کلمه کلیدی '),
                            TextInput::make('seo_words_concept')->label('کلمات هم کانسپت '),
                        ])->collapsed(),

                    ])->columnSpan(2),

                    Section::make()->schema([
                        TextInput::make('slug')->label('اسلاگ')->unique(ignoreRecord: true)->maxLength(255)->required(),
                        Select::make('category_id')->relationship('category', 'title')->label('دسته بندی')->required(),
                        FileUpload::make('image_name')->image()->label('تصویر')->imageEditor()->required(),
                        DateTimePicker::make('published_at')->label('تاریخ انتشار')->required(),
                        Toggle::make('is_show')->label('وضعیت نمایش')->required(),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان نمایش دهنده گوگل')->searchable(),
//                TextColumn::make('view_count')->label('تعداد نمایش'),
                TextColumn::make('link_view_article')->label('نمایش در سایت ')
                    ->url(fn(Article $article) => config('app.front_url') . "/articles/" . "{$article->slug}")
                    ->getStateUsing(fn(Article $article) => "{$article->slug}")
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->color('primary'),
                TextColumn::make('category.title')->label('دسته بندی'),
                TextColumn::make('author.name')->label('نویسنده'),
                IconColumn::make('is_show')->label('وضعیت نمایش')->boolean(),
                TextColumn::make('published_at')->label('تاریخ انتشار')->dateTime(),
                TextColumn::make('created_at')->label('ایجاد در')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('category')->label('دسته بندی')->relationship('category', 'title'),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
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
        return auth()->user()->isAdmin();
    }
}

