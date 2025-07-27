<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $modelLabel = 'دسته بندی مقالات';

    protected static ?string $pluralModelLabel = 'دسته بندی مقالات';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'محتوا';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Grid::make(1)->schema([
                        TextInput::make('name')->required()->label('نام')->maxLength(255),
                    ]),

                    Section::make()->schema([
                        Textarea::make('description')->label('توضیحات')->maxLength(65535)->nullable(),
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
                    FileUpload::make('image_url')->image()->label('تصویر')->imageEditor()->nullable(),
                    Toggle::make('is_show')->label('وضعیت نمایش')->default(true),
                ])->columnSpan(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('slug')->label('اسلاگ'),
                IconColumn::make('is_show')->label('وضعیت نمایش')->boolean(),
            ])
            ->filters([
                Filter::make('name')->form([
                    TextInput::make('name')->label('نام'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['name'],
                    fn(Builder $query, $data): Builder => $query->where('name', 'like', '%' . $data . '%')
                )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
        return auth()->user()->isAdmin();
    }
}
