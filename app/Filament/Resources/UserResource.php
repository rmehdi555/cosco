<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $pluralModelLabel = 'کاربران';

    protected static ?string $slug = 'users';

    protected static ?string $navigationGroup = 'مدیریت کاربران';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('first_name')
                ->label('نام')
                ->required()
                ->maxLength(255),

            TextInput::make('last_name')
                ->label('نام خانوادگی')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('ایمیل')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('cell_phone')
                ->label('شماره موبایل')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('phone')
                ->label('شماره تلفن')
                ->maxLength(255),

            Select::make('type')
                ->label('نوع کاربر')
                ->options([
                    UserType::SUPER_ADMIN->value => UserType::SUPER_ADMIN->label(),
                    UserType::ADMIN->value => UserType::ADMIN->label(),
                    UserType::SELLER->value => UserType::SELLER->label(),
                    UserType::USER->value => UserType::USER->label(),
                ])
                ->default(UserType::USER->value)
                ->required(),

            Toggle::make('is_active')
                ->label('فعال')
                ->default(true),

            TextInput::make('password')
                ->label('رمز عبور')
                ->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create')
                ->minLength(8),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('آی دی')
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('نام خانوادگی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cell_phone')
                    ->label('شماره موبایل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('نوع کاربر')
                    ->formatStateUsing(fn (UserType $state): string => $state->label())
                    ->badge()
                    ->color(fn (UserType $state): string => match($state) {
                        UserType::SUPER_ADMIN => 'danger',
                        UserType::ADMIN => 'warning',
                        UserType::SELLER => 'info',
                        UserType::USER => 'success',
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
