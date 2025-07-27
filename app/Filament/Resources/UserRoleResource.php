<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRoleResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserRoleResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $modelLabel = 'نقش کاربران';

    protected static ?string $pluralModelLabel = 'نقش کاربران';

    protected static ?string $slug = 'user-role';

    protected static ?string $navigationGroup = 'مدیریت کاربران';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('آی دی'),
                TextColumn::make('first_name')->label('نام'),
                TextColumn::make('last_name')->label('نام خانوادگی'),
                TextColumn::make('email')->label('ایمیل'),
                TextColumn::make('cell_phone')->label('شماره موبایل'),
                TextColumn::make('roles.name')->label('نقش'),
            ])
            ->filters([
                Filter::make('first_name')->form([
                    TextInput::make('first_name')->label('نام'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['first_name'],
                    fn(Builder $query, $data): Builder => $query->where('users.first_name', 'like', '%' . $data . '%'),
                )),

                Filter::make('cell_phone')->form([
                    TextInput::make('cell_phone')->label('شماره موبایل'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['cell_phone'],
                    fn(Builder $query, $data): Builder => $query->where('users.cell_phone', 'like', $data . '%'),
                )),

                Filter::make('email')->form([
                    TextInput::make('email')->label('ایمیل'),
                ])->query(fn(Builder $query, array $data): Builder => $query->when(
                    $data['email'],
                    fn(Builder $query, $data): Builder => $query->where('users.email', 'like', $data . '%'),
                )),
            ])
            ->actions([
                Action::make('roles')
                    ->label('نقش')
                    ->color('danger')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Select::make('permission')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->default(fn(User $record) => $record->roles ? $record->roles->pluck('id')->toArray() : [])
                            ->label('نقش ها')
                            ->preload()
                    ])
                    ->action(function (User $record, array $data) {
                        if (isset($data['permission'])) {
                            $record->syncRoles($data['permission']);
                        }
                        return $record;
                    }),

                Action::make('change_password')
                    ->label('تغییر رمز عبور')
                    ->color('warning')
                    ->icon('heroicon-o-lock-closed')
                    ->form([
                        TextInput::make('password')->label('رمز عبور')->required()->minLength(8),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update(['password' => Hash::make($data['password'])]);
                        return $record;
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserRoles::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
}
