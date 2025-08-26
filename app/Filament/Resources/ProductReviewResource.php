<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
                            ->relationship('parent', 'comment', function ($query) {
                                return $query->whereNotNull('comment')->where('comment', '!=', '');
                            })
                            ->nullable()
                            ->searchable()
                            ->placeholder('انتخاب نظر والد (اختیاری)')
                            ->helperText('اگر این نظر پاسخ به نظر دیگری است، آن را انتخاب کنید')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record ? 'نظر #' . $record->id . ' - ' . Str::limit($record->comment, 50) : null),
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

                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->helperText('آیا این نظر فعال است و نمایش داده می‌شود؟'),
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

                Section::make('تصاویر نظر')->schema([
                    FileUpload::make('review_images')
                        ->label('تصاویر نظر')
                        ->multiple()
                        ->image()
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('450')
                        ->disk('public')
                        ->directory('product-comments')
                        ->maxSize(2048)
                        ->helperText('تصاویر مرتبط با نظر - حداکثر 2 مگابایت برای هر تصویر')
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames()
                        ->afterStateUpdated(function ($state, $record) {
                            if ($record && $state) {
                                // Handle file uploads for existing records
                                $files = [];
                                foreach ($state as $file) {
                                    $files[] = [
                                        'product_review_id' => $record->id,
                                        'image_url' => $file,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }
                                \App\Models\ProductReviewFile::insert($files);
                            }
                        }),
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

                TextColumn::make('productReviewFile_count')
                    ->label('تعداد تصاویر')
                    ->counts('productReviewFile')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Filter::make('with_images')
                    ->label('دارای تصویر')
                    ->query(fn (Builder $query): Builder => $query->whereHas('productReviewFile')),

                Filter::make('without_images')
                    ->label('بدون تصویر')
                    ->query(fn (Builder $query): Builder => $query->whereDoesntHave('productReviewFile')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_images')
                    ->label('مشاهده تصاویر')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->visible(fn (?ProductReview $record): bool => $record && $record->productReviewFile->count() > 0)
                    ->modalHeading('تصاویر نظر')
                    ->modalContent(function (?ProductReview $record) {
                        if (!$record || $record->productReviewFile->count() === 0) {
                            return view('filament.components.no-images');
                        }
                        
                        return view('filament.components.review-images', [
                            'images' => $record->productReviewFile
                        ]);
                    })
                    ->modalWidth('4xl'),
                Tables\Actions\Action::make('approve')
                    ->label('تایید کردن')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (?ProductReview $record): bool => $record && !$record->approved)
                    ->requiresConfirmation()
                    ->modalHeading('تایید کردن نظر')
                    ->modalDescription('آیا مطمئن هستید که می‌خواهید این نظر را تایید کنید؟')
                    ->modalSubmitActionLabel('تایید کردن')
                    ->action(function (ProductReview $record): void {
                        $record->update(['approved' => true]);
                    })
                    ->after(function (ProductReview $record): void {
                        \Filament\Notifications\Notification::make()
                            ->title('نظر تایید شد')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('disapprove')
                    ->label('عدم تایید')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (?ProductReview $record): bool => $record && $record->approved)
                    ->requiresConfirmation()
                    ->modalHeading('عدم تایید نظر')
                    ->modalDescription('آیا مطمئن هستید که می‌خواهید این نظر را عدم تایید کنید؟')
                    ->modalSubmitActionLabel('عدم تایید')
                    ->action(function (ProductReview $record): void {
                        $record->update(['approved' => false]);
                    })
                    ->after(function (ProductReview $record): void {
                        \Filament\Notifications\Notification::make()
                            ->title('نظر عدم تایید شد')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('تایید کردن انتخاب شده‌ها')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('تایید کردن نظرات انتخاب شده')
                        ->modalDescription('آیا مطمئن هستید که می‌خواهید نظرات انتخاب شده را تایید کنید؟')
                        ->modalSubmitActionLabel('تایید کردن')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(function ($record) {
                                $record->update(['approved' => true]);
                            });
                        })
                        ->after(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            \Filament\Notifications\Notification::make()
                                ->title(count($records) . ' نظر تایید شد')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('disapprove')
                        ->label('عدم تایید انتخاب شده‌ها')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('عدم تایید نظرات انتخاب شده')
                        ->modalDescription('آیا مطمئن هستید که می‌خواهید نظرات انتخاب شده را عدم تایید کنید؟')
                        ->modalSubmitActionLabel('عدم تایید')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(function ($record) {
                                $record->update(['approved' => false]);
                            });
                        })
                        ->after(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            \Filament\Notifications\Notification::make()
                                ->title(count($records) . ' نظر عدم تایید شد')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user && $user->isAdmin();
    }
} 