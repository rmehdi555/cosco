<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponUsageResource\Pages;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\Order;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CouponUsageResource extends Resource
{
    protected static ?string $model = CouponUsage::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clock';
    }

    protected static ?string $modelLabel = 'استفاده از کوپن';

    protected static ?string $pluralModelLabel = 'استفاده‌های کوپن';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'فروشگاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Section::make('اطلاعات استفاده')->schema([
                        Select::make('coupon_id')
                            ->label('کوپن')
                            ->relationship('coupon', 'code')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب کوپن'),

                        Select::make('order_id')
                            ->label('سفارش')
                            ->relationship('order', 'id')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب سفارش'),

                        DateTimePicker::make('used_at')
                            ->label('تاریخ استفاده')
                            ->required()
                            ->default(now())
                            ->helperText('تاریخ و زمان استفاده از کوپن'),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('coupon.code')
                    ->label('کد کوپن')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('coupon.discount_percent')
                    ->label('درصد تخفیف')
                    ->suffix('%')
                    ->sortable()
                    ->visible(fn (?CouponUsage $record) => $record?->coupon?->discount_percent !== null),

                TextColumn::make('coupon.discount_amount')
                    ->label('مبلغ تخفیف')
                    ->money('IRR')
                    ->sortable()
                    ->visible(fn (?CouponUsage $record) => $record?->coupon?->discount_amount !== null),

                TextColumn::make('order.id')
                    ->label('شماره سفارش')
                    ->searchable()
                    ->sortable()
                    ->url(fn (?CouponUsage $record) => $record?->order ? route('filament.admin.resources.orders.edit', $record->order) : null),

                TextColumn::make('used_at')
                    ->label('تاریخ استفاده')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('coupon')
                    ->label('کوپن')
                    ->relationship('coupon', 'code')
                    ->searchable(),

                SelectFilter::make('order')
                    ->label('سفارش')
                    ->relationship('order', 'id')
                    ->searchable(),

                Filter::make('used_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('used_from')
                            ->label('از تاریخ'),
                        \Filament\Forms\Components\DatePicker::make('used_until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['used_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('used_at', '>=', $date),
                            )
                            ->when(
                                $data['used_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('used_at', '<=', $date),
                            );
                    }),

                Filter::make('today')
                    ->label('امروز')
                    ->query(fn (Builder $query): Builder => $query->whereDate('used_at', today())),

                Filter::make('this_week')
                    ->label('این هفته')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('used_at', [now()->startOfWeek(), now()->endOfWeek()])),

                Filter::make('this_month')
                    ->label('این ماه')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('used_at', now()->month)->whereYear('used_at', now()->year)),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('used_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouponUsages::route('/'),
            'create' => Pages\CreateCouponUsage::route('/create'),
            'view' => Pages\ViewCouponUsage::route('/{record}'),
            'edit' => Pages\EditCouponUsage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 