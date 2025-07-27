<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Product;
use Filament\Forms\Components\Grid;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'نظر محصول';

    protected static ?string $pluralModelLabel = 'نظرات محصولات';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'محصولات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('اطلاعات نظر')->schema([
                        Select::make('user_id')
                            ->label('کاربر')
                            ->relationship('user', 'first_name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب کاربر')
                            ->helperText('کاربری که نظر داده است'),

                        Select::make('product_id')
                            ->label('محصول')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->placeholder('انتخاب محصول')
                            ->helperText('محصول مورد نظر'),

                        Select::make('parent_id')
                            ->label('پاسخ به')
                            ->relationship('parent', 'id')
                            ->nullable()
                            ->searchable()
                            ->placeholder('انتخاب نظر والد (اختیاری)')
                            ->helperText('اگر این نظر پاسخ به نظر دیگری است، آن را انتخاب کنید'),
                    ])->columnSpan(1),

                    Section::make('محتوای نظر')->schema([
                        TextInput::make('rating')
                            ->label('امتیاز')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->helperText('امتیاز از 1 تا 5'),

                        Toggle::make('approved')
                            ->label('تایید شده')
                            ->default(false)
                            ->helperText('آیا این نظر تایید شده است؟'),
                    ])->columnSpan(1),
                ]),

                Section::make('متن نظر')->schema([
                    Textarea::make('comment')
                        ->label('متن نظر')
                        ->required()
                        ->rows(5)
                        ->placeholder('متن نظر کاربر را وارد کنید')
                        ->helperText('متن کامل نظر کاربر'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شماره نظر')
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

                TextColumn::make('product.name')
                    ->label('محصول')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn (?ProductReview $record) => $record?->product ? route('filament.admin.resources.products.edit', $record->product) : null),

                TextColumn::make('product.category.name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rating')
                    ->label('امتیاز')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        5 => 'success',
                        4 => 'success',
                        3 => 'warning',
                        2 => 'danger',
                        1 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),

                TextColumn::make('comment')
                    ->label('نظر')
                    ->limit(100)
                    ->tooltip(fn (?ProductReview $record) => $record?->comment)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.id')
                    ->label('پاسخ به')
                    ->sortable()
                    ->placeholder('نظر اصلی')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('replies_count')
                    ->label('تعداد پاسخ‌ها')
                    ->counts('replies')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('approved')
                    ->label('تایید شده')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

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
                SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'first_name')
                    ->searchable(),

                SelectFilter::make('product')
                    ->label('محصول')
                    ->relationship('product', 'name')
                    ->searchable(),

                SelectFilter::make('product_category')
                    ->label('دسته‌بندی محصول')
                    ->relationship('product.category', 'name')
                    ->searchable(),

                SelectFilter::make('rating')
                    ->label('امتیاز')
                    ->options([
                        1 => '⭐ 1 ستاره',
                        2 => '⭐⭐ 2 ستاره',
                        3 => '⭐⭐⭐ 3 ستاره',
                        4 => '⭐⭐⭐⭐ 4 ستاره',
                        5 => '⭐⭐⭐⭐⭐ 5 ستاره',
                    ]),

                TernaryFilter::make('approved')
                    ->label('وضعیت تایید')
                    ->placeholder('همه نظرات')
                    ->trueLabel('فقط نظرات تایید شده')
                    ->falseLabel('فقط نظرات تایید نشده'),

                Filter::make('parent_reviews')
                    ->label('نظرات اصلی')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),

                Filter::make('reply_reviews')
                    ->label('پاسخ‌ها')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),

                Filter::make('high_rating')
                    ->label('امتیاز بالا (4-5)')
                    ->query(fn (Builder $query): Builder => $query->whereIn('rating', [4, 5])),

                Filter::make('low_rating')
                    ->label('امتیاز پایین (1-2)')
                    ->query(fn (Builder $query): Builder => $query->whereIn('rating', [1, 2])),

                Filter::make('comment')
                    ->form([
                        TextInput::make('comment')
                            ->label('جستجو در متن نظر')
                            ->placeholder('متن نظر را وارد کنید'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['comment'],
                                fn (Builder $query, $comment): Builder => $query->where('comment', 'like', "%{$comment}%"),
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

                Filter::make('pending_approval')
                    ->label('در انتظار تایید')
                    ->query(fn (Builder $query): Builder => $query->where('approved', false)),
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
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'view' => Pages\ViewProductReview::route('/{record}'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
} 