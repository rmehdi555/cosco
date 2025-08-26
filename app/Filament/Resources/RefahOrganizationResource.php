<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefahOrganizationResource\Pages;
use App\Models\RefahOrganization;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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

class RefahOrganizationResource extends Resource
{
    protected static ?string $model = RefahOrganization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $modelLabel = 'سازمان رفاه';

    protected static ?string $pluralModelLabel = 'سازمان‌های رفاه';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'مدیریت رفاه';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات سازمان رفاه')
                    ->description('اطلاعات اصلی سازمان رفاه را وارد کنید')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان سازمان')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('عنوان سازمان رفاه را وارد کنید')
                            ->helperText('عنوان کامل سازمان یا شرکت رفاه'),

                        Toggle::make('is_active')
                            ->label('وضعیت فعال')
                            ->default(true)
                            ->helperText('آیا این سازمان در حال حاضر فعال است؟')
                            ->inline(false),
                    ])
                    ->columns(1),
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
                    ->label('عنوان سازمان')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('medium'),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('refah_users_count')
                    ->label('تعداد کاربران')
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
            'index' => Pages\ListRefahOrganizations::route('/'),
            'create' => Pages\CreateRefahOrganization::route('/create'),
            'view' => Pages\ViewRefahOrganization::route('/{record}'),
            'edit' => Pages\EditRefahOrganization::route('/{record}/edit'),
        ];
    }
}
