<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Models\ContactUs;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت کاربران';
    }

    protected static ?string $navigationLabel = 'پیام‌های تماس با ما';

    protected static ?string $modelLabel = 'پیام تماس با ما';

    protected static ?string $pluralModelLabel = 'پیام‌های تماس با ما';

    public static function canViewAny(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات شخصی')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('نام')
                            ->maxLength(191),

                        Forms\Components\TextInput::make('last_name')
                            ->label('نام خانوادگی')
                            ->maxLength(191),

                        Forms\Components\TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->maxLength(191),

                        Forms\Components\TextInput::make('phone')
                            ->label('شماره تلفن')
                            ->tel()
                            ->maxLength(191),
                    ])
                    ->columns(2),

                Section::make('متن پیام')
                    ->schema([
                        Forms\Components\Textarea::make('body')
                            ->label('متن پیام')
                            ->required()
                            ->minLength(10)
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('وضعیت')
                    ->schema([
                        Forms\Components\Toggle::make('is_answered')
                            ->label('پاسخ داده شده')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('نام کامل')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('ایمیل کپی شد')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('phone')
                    ->label('شماره تلفن')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('شماره تلفن کپی شد')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('body')
                    ->label('متن پیام')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->body;
                    }),

                Tables\Columns\IconColumn::make('is_answered')
                    ->label('وضعیت پاسخ')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ارسال')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخرین به‌روزرسانی')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_answered')
                    ->label('وضعیت پاسخ')
                    ->placeholder('همه')
                    ->trueLabel('پاسخ داده شده')
                    ->falseLabel('بدون پاسخ'),

                Tables\Filters\Filter::make('has_email')
                    ->label('دارای ایمیل')
                    ->query(fn ($query) => $query->whereNotNull('email')),

                Tables\Filters\Filter::make('has_phone')
                    ->label('دارای شماره تلفن')
                    ->query(fn ($query) => $query->whereNotNull('phone')),
            ])
            ->actions([

                Actions\EditAction::make()
                    ->label('ویرایش'),

                Actions\Action::make('mark_answered')
                    ->label('علامت‌گذاری پاسخ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record && !$record->is_answered)
                    ->action(function ($record) {
                        $record->update(['is_answered' => true]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('علامت‌گذاری به عنوان پاسخ داده شده')
                    ->modalDescription('آیا مطمئن هستید که می‌خواهید این پیام را به عنوان پاسخ داده شده علامت‌گذاری کنید؟')
                    ->modalSubmitActionLabel('بله، علامت‌گذاری کن'),

                Actions\Action::make('mark_unanswered')
                    ->label('علامت‌گذاری بدون پاسخ')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record && $record->is_answered)
                    ->action(function ($record) {
                        $record->update(['is_answered' => false]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('علامت‌گذاری به عنوان بدون پاسخ')
                    ->modalDescription('آیا مطمئن هستید که می‌خواهید این پیام را به عنوان بدون پاسخ علامت‌گذاری کنید؟')
                    ->modalSubmitActionLabel('بله، علامت‌گذاری کن'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('mark_answered')
                        ->label('علامت‌گذاری پاسخ داده شده')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_answered' => true]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('علامت‌گذاری به عنوان پاسخ داده شده')
                        ->modalDescription('آیا مطمئن هستید که می‌خواهید پیام‌های انتخاب شده را به عنوان پاسخ داده شده علامت‌گذاری کنید؟')
                        ->modalSubmitActionLabel('بله، علامت‌گذاری کن'),

                    Actions\BulkAction::make('mark_unanswered')
                        ->label('علامت‌گذاری بدون پاسخ')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_answered' => false]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('علامت‌گذاری به عنوان بدون پاسخ')
                        ->modalDescription('آیا مطمئن هستید که می‌خواهید پیام‌های انتخاب شده را به عنوان بدون پاسخ علامت‌گذاری کنید؟')
                        ->modalSubmitActionLabel('بله، علامت‌گذاری کن'),

                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListContactUs::route('/'),
            'create' => Pages\CreateContactUs::route('/create'),
            'view' => Pages\ViewContactUs::route('/{record}'),
            'edit' => Pages\EditContactUs::route('/{record}/edit'),
        ];
    }
}
