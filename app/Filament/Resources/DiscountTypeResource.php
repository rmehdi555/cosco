<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountTypeResource\Pages;
use App\Models\DiscountType;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountTypeResource extends Resource
{
    protected static ?string $model = DiscountType::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت سایت';
    }

    protected static ?string $navigationLabel = 'طراحی صفحه اول';

    protected static ?string $modelLabel = 'طرح صفحه اول';

    protected static ?string $pluralModelLabel = 'طرح صفحه اول';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\Select::make('product_category_id')
                            ->label('دسته‌بندی محصول')
                            ->relationship('productCategory', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('footer')
                            ->label('پاورقی')
                            ->maxLength(255),

                        Forms\Components\ColorPicker::make('background_color_up')
                            ->label('رنگ پس‌زمینه بالا'),

                        Forms\Components\ColorPicker::make('background_color_down')
                            ->label('رنگ پس‌زمینه پایین'),

                        Forms\Components\FileUpload::make('image_url')
                            ->label('تصویر')
                            ->image()
                            ->disk('public')
                            ->directory('discount-types'),

                        Forms\Components\TextInput::make('link')
                            ->label('لینک')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('target')
                            ->label('باز شدن در تب جدید')
                            ->default(true),

                        Forms\Components\Toggle::make('is_show')
                            ->label('نمایش')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('اطلاعات تبلیغات')
                    ->schema([
                        Forms\Components\TextInput::make('ads_title')
                            ->label('عنوان تبلیغات')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('ads_footer')
                            ->label('پاورقی تبلیغات')
                            ->maxLength(255),

                        Forms\Components\ColorPicker::make('ads_background_color_up')
                            ->label('رنگ پس‌زمینه تبلیغات بالا'),

                        Forms\Components\ColorPicker::make('ads_background_color_down')
                            ->label('رنگ پس‌زمینه تبلیغات پایین'),

                        Forms\Components\FileUpload::make('ads_image_url')
                            ->label('تصویر تبلیغات')
                            ->image()
                            ->disk('public')
                            ->directory('discount-types/ads'),

                        Forms\Components\TextInput::make('ads_link')
                            ->label('لینک تبلیغات')
                            ->required()
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('ads_target')
                            ->label('باز شدن تبلیغات در تب جدید')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('دسته‌بندی محصول')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_url')
                    ->label('تصویر')
                    ->disk('public')
                    ->size(60),

                Tables\Columns\TextColumn::make('link')
                    ->label('لینک')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('target')
                    ->label('تب جدید')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_show')
                    ->label('نمایش')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ads_background_color_up')
                    ->label('رنگ پس‌زمینه تبلیغات بالا')
                    ->color(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ads_background_color_down')
                    ->label('رنگ پس‌زمینه تبلیغات پایین')
                    ->color(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label('دسته‌بندی محصول')
                    ->relationship('productCategory', 'name'),

                Tables\Filters\TernaryFilter::make('is_show')
                    ->label('نمایش'),

                Tables\Filters\TrashedFilter::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscountTypes::route('/'),
            'create' => Pages\CreateDiscountType::route('/create'),
            'view' => Pages\ViewDiscountType::route('/{record}'),
            'edit' => Pages\EditDiscountType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
