<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartItemResource\Pages;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\CartStatus;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $modelLabel = 'آیتم سبد خرید';

    protected static ?string $pluralModelLabel = 'آیتم‌های سبد خرید';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'فروشگاه';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('اطلاعات سبد خرید')->schema([
                        Select::make('cart_id')
                            ->label('سبد خرید')
                            ->relationship('cart', 'id')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب سبد خرید')
                            ->helperText('سبد خرید مربوط به این آیتم'),

                        Select::make('product_id')
                            ->label('محصول')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب محصول')
                            ->helperText('محصول انتخاب شده')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('price', $product->price);
                                    }
                                }
                            }),
                    ])->columnSpan(1),

                    Section::make('قیمت و تعداد')->schema([
                        TextInput::make('quantity')
                            ->label('تعداد')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(1)
                            ->helperText('تعداد محصول در سبد خرید'),

                        TextInput::make('price')
                            ->label('قیمت واحد')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('تومان')
                            ->required()
                            ->helperText('قیمت هر واحد محصول'),

                        TextInput::make('total_price')
                            ->label('قیمت کل')
                            ->disabled()
                            ->prefix('تومان')
                            ->helperText('قیمت کل = تعداد × قیمت واحد')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $record = $component->getRecord();
                                if ($record) {
                                    $component->state($record->total_price);
                                }
                            }),
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

                TextColumn::make('cart.id')
                    ->label('شماره سبد خرید')
                    ->searchable()
                    ->sortable()
                    ->url(fn (?CartItem $record) => $record?->cart ? route('filament.admin.resources.carts.edit', $record->cart) : null),

                TextColumn::make('cart.user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cart.status')
                    ->label('وضعیت سبد')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'در انتظار',
                        'paid' => 'پرداخت شده',
                        'shipped' => 'ارسال شده',
                        'delivered' => 'تحویل داده شده',
                        'cancelled' => 'لغو شده',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.name')
                    ->label('محصول')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn (?CartItem $record) => $record?->product ? route('filament.admin.resources.products.edit', $record->product) : null),

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

                TextColumn::make('quantity')
                    ->label('تعداد')
                    ->sortable()
                    ->badge(),

                TextColumn::make('price')
                    ->label('قیمت واحد')
                    ->money('IRR')
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('قیمت کل')
                    ->money('IRR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

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
                SelectFilter::make('cart')
                    ->label('سبد خرید')
                    ->relationship('cart', 'id')
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

                Filter::make('quantity')
                    ->form([
                        TextInput::make('min_quantity')
                            ->label('حداقل تعداد')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('max_quantity')
                            ->label('حداکثر تعداد')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_quantity'],
                                fn (Builder $query, $quantity): Builder => $query->where('quantity', '>=', $quantity),
                            )
                            ->when(
                                $data['max_quantity'],
                                fn (Builder $query, $quantity): Builder => $query->where('quantity', '<=', $quantity),
                            );
                    }),

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

                Filter::make('total_price')
                    ->form([
                        TextInput::make('min_total')
                            ->label('حداقل قیمت کل')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('max_total')
                            ->label('حداکثر قیمت کل')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_total'],
                                fn (Builder $query, $total): Builder => $query->whereRaw('quantity * price >= ?', [$total]),
                            )
                            ->when(
                                $data['max_total'],
                                fn (Builder $query, $total): Builder => $query->whereRaw('quantity * price <= ?', [$total]),
                            );
                    }),

                Filter::make('pending_carts')
                    ->label('سبدهای خرید در انتظار')
                    ->query(fn (Builder $query): Builder => $query->whereHas('cart', fn ($q) => $q->where('status', 'pending'))),

                Filter::make('paid_carts')
                    ->label('سبدهای خرید پرداخت شده')
                    ->query(fn (Builder $query): Builder => $query->whereHas('cart', fn ($q) => $q->where('status', 'paid'))),

                Filter::make('high_value_items')
                    ->label('آیتم‌های با ارزش بالا')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('quantity * price >= 1000000')), // 1 million IRR

                Filter::make('bulk_items')
                    ->label('آیتم‌های عمده')
                    ->query(fn (Builder $query): Builder => $query->where('quantity', '>=', 5)),

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
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'view' => Pages\ViewCartItem::route('/{record}'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin();
    }
} 