<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefahCartResource\Pages;
use App\Models\RefahCart;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RefahCartResource extends Resource
{
    protected static ?string $model = RefahCart::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    protected static ?string $modelLabel = 'بسته رفاهی';

    protected static ?string $pluralModelLabel = 'بسته‌های رفاهی';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت رفاه';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات بسته رفاهی')
                    ->description('اطلاعات اصلی بسته رفاهی را وارد کنید')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('عنوان بسته')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('عنوان بسته رفاهی را وارد کنید')
                                ->helperText('عنوان کامل بسته رفاهی')
                                ->columnSpan(1),

                            TextInput::make('price')
                                ->label('قیمت (ریال)')
                                ->required()
                                ->numeric()
                                ->prefix('ریال')
                                ->placeholder('0')
                                ->helperText('قیمت بسته به ریال')
                                ->columnSpan(1),
                        ]),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->placeholder('توضیحات بسته رفاهی را وارد کنید')
                            ->helperText('توضیحات تکمیلی در مورد محتویات بسته')
                            ->rows(4)
                            ->columnSpan(2),

                        Toggle::make('is_active')
                            ->label('وضعیت فعال')
                            ->default(true)
                            ->helperText('آیا این بسته در حال حاضر قابل انتخاب است؟')
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('عنوان بسته')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('medium')
                    ->limit(50),

                TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(100)
                    ->wrap()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('قیمت')
                    ->money('IRR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('refah_users_count')
                    ->label('تعداد انتخاب‌ها')
                    ->counts('refahUsers')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('وضعیت فعال')
                    ->boolean()
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال')
                    ->native(false),
            ])
            ->actions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListRefahCarts::route('/'),
            'create' => Pages\CreateRefahCart::route('/create'),
            'view' => Pages\ViewRefahCart::route('/{record}'),
            'edit' => Pages\EditRefahCart::route('/{record}/edit'),
        ];
    }
}
