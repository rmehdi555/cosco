<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistItemResource\Pages;
use App\Models\WishlistItem;
use App\Models\Wishlist;
use App\Models\Product;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WishlistItemResource extends Resource
{
    protected static ?string $model = WishlistItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $modelLabel = 'آیتم لیست علاقه';

    protected static ?string $pluralModelLabel = 'آیتم‌های لیست علاقه';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'مدیریت کاربران';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('اطلاعات لیست علاقه')->schema([
                        Select::make('wishlist_id')
                            ->label('لیست علاقه')
                            ->relationship('wishlist', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب لیست علاقه')
                            ->helperText('لیست علاقه مربوط به این آیتم'),

                        Select::make('product_id')
                            ->label('محصول')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب محصول')
                            ->helperText('محصول اضافه شده به لیست علاقه'),
                    ])->columnSpan(1),

                    Section::make('اطلاعات اضافی')->schema([
                        TextColumn::make('wishlist_user')
                            ->label('کاربر لیست علاقه')
                            ->state(fn ($record) => $record?->wishlist?->user?->first_name ?? 'نامشخص')
                            ->disabled()
                            ->helperText('کاربر صاحب این لیست علاقه'),

                        TextColumn::make('product_price')
                            ->label('قیمت محصول')
                            ->state(fn ($record) => $record?->product?->price ? number_format($record->product->price) . ' تومان' : 'نامشخص')
                            ->disabled()
                            ->helperText('قیمت فعلی محصول'),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شماره آیتم')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('wishlist.name')
                    ->label('نام لیست علاقه')
                    ->searchable()
                    ->sortable()
                    ->url(fn (?WishlistItem $record) => $record?->wishlist ? route('filament.admin.resources.wishlists.edit', $record->wishlist) : null),

                TextColumn::make('wishlist.user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('wishlist.user.email')
                    ->label('ایمیل')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.name')
                    ->label('محصول')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn (?WishlistItem $record) => $record?->product ? route('filament.admin.resources.products.edit', $record->product) : null),

                TextColumn::make('product.category.name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.brand.name')
                    ->label('برند')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.price')
                    ->label('قیمت محصول')
                    ->money('IRR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.stock')
                    ->label('موجودی')
                    ->sortable()
                    ->color(fn (int $state): string => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاریخ اضافه شدن')
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
                SelectFilter::make('wishlist')
                    ->label('لیست علاقه')
                    ->relationship('wishlist', 'name')
                    ->searchable(),

                SelectFilter::make('wishlist_user')
                    ->label('کاربر')
                    ->relationship('wishlist.user', 'first_name')
                    ->searchable(),

                SelectFilter::make('product')
                    ->label('محصول')
                    ->relationship('product', 'name')
                    ->searchable(),

                SelectFilter::make('product_category')
                    ->label('دسته‌بندی محصول')
                    ->relationship('product.category', 'name')
                    ->searchable(),

                SelectFilter::make('product_brand')
                    ->label('برند محصول')
                    ->relationship('product.brand', 'name')
                    ->searchable(),

                Filter::make('out_of_stock_products')
                    ->label('محصولات ناموجود')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('stock', 0))),

                Filter::make('low_stock_products')
                    ->label('محصولات با موجودی کم')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('stock', '>', 0)->where('stock', '<=', 10))),

                Filter::make('expensive_products')
                    ->label('محصولات گران')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('price', '>=', 1000000))), // 1 million IRR

                Filter::make('cheap_products')
                    ->label('محصولات ارزان')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('price', '<=', 500000))), // 500k IRR

                Filter::make('featured_products')
                    ->label('محصولات ویژه')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('is_featured', true))),

                Filter::make('active_products')
                    ->label('محصولات فعال')
                    ->query(fn (Builder $query): Builder => $query->whereHas('product', fn ($q) => $q->where('is_active', true))),

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

                Filter::make('recent_additions')
                    ->label('اضافه شده اخیراً')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
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
            'index' => Pages\ListWishlistItems::route('/'),
            'create' => Pages\CreateWishlistItem::route('/create'),
            'view' => Pages\ViewWishlistItem::route('/{record}'),
            'edit' => Pages\EditWishlistItem::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 