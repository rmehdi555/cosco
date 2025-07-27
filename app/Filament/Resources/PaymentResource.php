<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $modelLabel = 'پرداخت';

    protected static ?string $pluralModelLabel = 'پرداخت‌ها';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'فروشگاه';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('اطلاعات پرداخت')->schema([
                        Select::make('order_id')
                            ->label('سفارش')
                            ->relationship('order', 'id')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب سفارش'),

                        Select::make('method')
                            ->label('روش پرداخت')
                            ->options(PaymentMethod::getOptions())
                            ->required()
                            ->placeholder('انتخاب روش پرداخت'),

                        Select::make('status')
                            ->label('وضعیت پرداخت')
                            ->options(PaymentStatus::getOptions())
                            ->required()
                            ->default(PaymentStatus::PENDING->value)
                            ->placeholder('انتخاب وضعیت')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === PaymentStatus::COMPLETED->value) {
                                    $set('paid_at', now());
                                } else {
                                    $set('paid_at', null);
                                }
                            }),

                        DateTimePicker::make('paid_at')
                            ->label('تاریخ پرداخت')
                            ->nullable()
                            ->helperText('تاریخ و زمان تکمیل پرداخت')
                            ->visible(fn (callable $get) => $get('status') === PaymentStatus::COMPLETED->value),
                    ])->columnSpan(1),

                    Section::make('مبلغ')->schema([
                        TextInput::make('amount')
                            ->label('مبلغ پرداخت')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('تومان')
                            ->required()
                            ->helperText('مبلغ پرداخت شده'),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شماره پرداخت')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('order.id')
                    ->label('شماره سفارش')
                    ->searchable()
                    ->sortable()
                    ->url(fn (?Payment $record) => $record?->order ? route('filament.admin.resources.orders.edit', $record->order) : null),

                TextColumn::make('order.user.first_name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('method')
                    ->label('روش پرداخت')
                    ->color(fn (PaymentMethod $state): string => $state->getColor())
                    ->formatStateUsing(fn (PaymentMethod $state): string => $state->getLabel())
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->color(fn (PaymentStatus $state): string => $state->getColor())
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->getLabel())
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('مبلغ')
                    ->money('IRR')
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('تاریخ پرداخت')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('هنوز پرداخت نشده'),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('order')
                    ->label('سفارش')
                    ->relationship('order', 'id')
                    ->searchable(),

                SelectFilter::make('method')
                    ->label('روش پرداخت')
                    ->options(PaymentMethod::getOptions()),

                SelectFilter::make('status')
                    ->label('وضعیت پرداخت')
                    ->options(PaymentStatus::getOptions()),

                Filter::make('amount')
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
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),

                Filter::make('paid_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('paid_from')
                            ->label('از تاریخ'),
                        \Filament\Forms\Components\DatePicker::make('paid_until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['paid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '>=', $date),
                            )
                            ->when(
                                $data['paid_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '<=', $date),
                            );
                    }),

                Filter::make('completed_today')
                    ->label('تکمیل شده امروز')
                    ->query(fn (Builder $query): Builder => $query->where('status', PaymentStatus::COMPLETED->value)->whereDate('paid_at', today())),

                Filter::make('pending_payments')
                    ->label('پرداخت‌های در انتظار')
                    ->query(fn (Builder $query): Builder => $query->where('status', PaymentStatus::PENDING->value)),

                Filter::make('failed_payments')
                    ->label('پرداخت‌های ناموفق')
                    ->query(fn (Builder $query): Builder => $query->where('status', PaymentStatus::FAILED->value)),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 