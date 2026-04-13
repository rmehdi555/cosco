<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use App\Models\Order;
use Filament\Actions;
use App\Models\Product;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-bag';
    }

    protected static ?string $modelLabel = 'آیتم سفارش';

    protected static ?string $pluralModelLabel = 'آیتم‌های سفارش';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'فروشگاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Section::make('اطلاعات سفارش')->schema([
                        Select::make('order_id')
                            ->label('سفارش')
                            ->relationship('order', 'id')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب سفارش')
                            ->helperText('سفارش مربوط به این آیتم'),

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

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->placeholder('توضیحات اضافی برای این آیتم...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('توضیحات اختیاری برای آیتم سفارش'),
                    ])->columnSpan(1),

                    Section::make('قیمت و تعداد')->schema([
                        TextInput::make('quantity')
                            ->label('تعداد')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(1)
                            ->helperText('تعداد محصول سفارش شده'),

                        TextInput::make('price')
                            ->label('قیمت واحد')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ریال')
                            ->required()
                            ->helperText('قیمت هر واحد محصول'),

                        TextInput::make('total_price')
                            ->label('قیمت کل')
                            ->disabled()
                            ->prefix('ریال')
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

                TextColumn::make('order.id')
                    ->label('شماره سفارش')
                    ->searchable()
                    ->sortable()
                    ->url(fn (?OrderItem $record) => $record?->order ? route('filament.admin.resources.orders.edit', $record->order) : null),

                TextColumn::make('order.user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('محصول')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn (?OrderItem $record) => $record?->product ? route('filament.admin.resources.products.edit', $record->product) : null),

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

                TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('order')
                    ->label('سفارش')
                    ->relationship('order', 'id')
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

                Filter::make('high_value_items')
                    ->label('آیتم‌های با ارزش بالا')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('quantity * price >= 1000000')), // 1 million IRR

                Filter::make('bulk_orders')
                    ->label('سفارش‌های عمده')
                    ->query(fn (Builder $query): Builder => $query->where('quantity', '>=', 10)),

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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'view' => Pages\ViewOrderItem::route('/{record}'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 