<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Enums\OrderPaymentStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Filament\ExcelExport\OrderExport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-bag';
    }

    protected static ?string $modelLabel = 'سفارش';

    protected static ?string $pluralModelLabel = 'سفارش‌ها';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'فروشگاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('کاربر')
                    ->relationship('user', 'first_name')
                    ->required()
                    ->searchable()
                    ->placeholder('انتخاب کاربر'),

                Select::make('status')
                    ->label('وضعیت سفارش')
                    ->options(OrderStatus::getOptions())
                    ->required()
                    ->default(OrderStatus::PENDING->value)
                    ->placeholder('انتخاب وضعیت سفارش'),

                Select::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options(OrderPaymentStatus::getOptions())
                    ->required()
                    ->default(OrderPaymentStatus::UNPAID->value)
                    ->placeholder('انتخاب وضعیت پرداخت'),

                TextInput::make('total_amount')
                    ->label('مبلغ کل')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('ریال')
                    ->required()
                    ->helperText('مبلغ کل سفارش'),

                Select::make('shipping_address_id')
                    ->label('آدرس ارسال')
                    ->relationship('shippingAddress', 'address')
                    ->required()
                    ->searchable()
                    ->placeholder('انتخاب آدرس ارسال'),

                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->placeholder('توضیحات سفارش...')
                    ->helperText('توضیحات اضافی در مورد سفارش'),

                DatePicker::make('received_at')
                    ->label('تاریخ دریافت')
                    ->helperText('تاریخ دریافت سفارش توسط مشتری')
                    ->placeholder('انتخاب تاریخ دریافت'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شماره سفارش')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('ایمیل')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('status')
                    ->label('وضعیت سفارش')
                    ->color(fn (OrderStatus $state): string => $state->getColor())
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->sortable(),

                BadgeColumn::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->color(fn (OrderPaymentStatus $state): string => $state->getColor())
                    ->formatStateUsing(fn (OrderPaymentStatus $state): string => $state->getLabel())
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('مبلغ کل')
                    ->money('IRR')
                    ->sortable(),

                TextColumn::make('shippingAddress.address')
                    ->label('آدرس ارسال')
                    ->limit(50)
                    ->tooltip(fn (?Order $record) => $record?->shippingAddress?->address)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50)
                    ->tooltip(fn (?Order $record) => $record?->description)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('received_at')
                    ->label('تاریخ دریافت')
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
                SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'first_name')
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('وضعیت سفارش')
                    ->options(OrderStatus::getOptions()),

                SelectFilter::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options(OrderPaymentStatus::getOptions()),

                Filter::make('total_amount')
                    ->form([
                        TextInput::make('min_amount')
                            ->label('حداقل مبلغ')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('max_amount')
                            ->label('حداکثر مبلغ')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),

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
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('خروجی Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return OrderExport::exportAll();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\Action::make('export_selected')
                        ->label('خروجی انتخاب شده‌ها')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            return OrderExport::exportSelected($records);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user && $user->isAdmin();
    }
} 