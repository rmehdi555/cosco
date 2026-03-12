<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    protected static ?string $modelLabel = 'کوپن تخفیف';

    protected static ?string $pluralModelLabel = 'کوپن‌های تخفیف';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'فروشگاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Section::make('اطلاعات کوپن')->schema([
                        TextInput::make('code')
                            ->label('کد کوپن')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('مثال: SUMMER2024')
                            ->helperText('کد کوپن باید منحصر به فرد باشد')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('code', strtoupper($state))),

                        Toggle::make('is_percentage')
                            ->label('تخفیف درصدی')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('discount_amount', null);
                                } else {
                                    $set('discount_percent', null);
                                }
                            }),

                        TextInput::make('discount_percent')
                            ->label('درصد تخفیف')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (callable $get) => $get('is_percentage'))
                            ->required(fn (callable $get) => $get('is_percentage')),

                        TextInput::make('discount_amount')
                            ->label('مبلغ تخفیف')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ریال')
                            ->visible(fn (callable $get) => !$get('is_percentage'))
                            ->required(fn (callable $get) => !$get('is_percentage')),
                    ])->columnSpan(1),

                    Section::make('تنظیمات استفاده')->schema([
                        DateTimePicker::make('expires_at')
                            ->label('تاریخ انقضا')
                            ->required()
                            ->minDate(now())
                            ->helperText('تاریخ و زمان انقضای کوپن'),

                        TextInput::make('max_uses')
                            ->label('حداکثر تعداد استفاده')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('تعداد دفعاتی که این کوپن قابل استفاده است'),

                        TextInput::make('used_count')
                            ->label('تعداد استفاده شده')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabled()
                            ->helperText('این مقدار به صورت خودکار به‌روزرسانی می‌شود'),
                    ])->columnSpan(1),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('کد کوپن')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('discount_percent')
                    ->label('درصد تخفیف')
                    ->suffix('%')
                    ->sortable()
                    ->visible(fn (?Coupon $record) => $record?->discount_percent !== null),

                TextColumn::make('discount_amount')
                    ->label('مبلغ تخفیف')
                    ->money('IRR')
                    ->sortable()
                    ->visible(fn (?Coupon $record) => $record?->discount_amount !== null),

                TextColumn::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->dateTime()
                    ->sortable()
                    ->color(fn (?Coupon $record) => $record?->expires_at?->isPast() ? 'danger' : 'success'),

                TextColumn::make('used_count')
                    ->label('استفاده شده')
                    ->sortable(),

                TextColumn::make('max_uses')
                    ->label('حداکثر استفاده')
                    ->sortable(),

                TextColumn::make('usage_percentage')
                    ->label('درصد استفاده')
                    ->getStateUsing(fn (?Coupon $record) => $record && $record->max_uses > 0 ? round(($record->used_count / $record->max_uses) * 100, 1) : 0)
                    ->suffix('%')
                    ->color(fn (?Coupon $record) => $record && $record->max_uses > 0 && ($record->used_count / $record->max_uses) >= 0.8 ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->label('وضعیت')
                    ->getStateUsing(function (?Coupon $record) {
                        if (!$record) return '';
                        if ($record->expires_at->isPast()) {
                            return 'منقضی شده';
                        }
                        if ($record->used_count >= $record->max_uses) {
                            return 'تمام شده';
                        }
                        return 'فعال';
                    })
                    ->badge()
                    ->color(function (?Coupon $record) {
                        if (!$record) return 'gray';
                        if ($record->expires_at->isPast()) {
                            return 'danger';
                        }
                        if ($record->used_count >= $record->max_uses) {
                            return 'warning';
                        }
                        return 'success';
                    }),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->label('فقط فعال')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '>', now())->whereRaw('used_count < max_uses')),

                Filter::make('expired')
                    ->label('منقضی شده')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<=', now())),

                Filter::make('fully_used')
                    ->label('تمام شده')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('used_count >= max_uses')),

                Filter::make('code')
                    ->form([
                        TextInput::make('code')
                            ->label('کد کوپن'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['code'],
                        fn (Builder $query, $data): Builder => $query->where('code', 'like', '%' . strtoupper($data) . '%'),
                    )),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'view' => Pages\ViewCoupon::route('/{record}'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 