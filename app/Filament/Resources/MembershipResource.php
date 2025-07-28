<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipResource\Pages;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'مدیریت عضویت';

    protected static ?string $navigationLabel = 'اعضا';

    protected static ?string $modelLabel = 'عضو';

    protected static ?string $pluralModelLabel = 'اعضا';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات عضویت')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('شماره سریال')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('کاربر')
                            ->options(User::all()->pluck('first_name', 'id'))
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('membership_type_id')
                            ->label('نوع عضویت')
                            ->options(MembershipType::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->label('تاریخ شروع')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('تاریخ پایان')
                            ->required()
                            ->after('start_date'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('شماره سریال')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('نام کاربر')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('membershipType.name')
                    ->label('نوع عضویت')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاریخ شروع')
                    ->date('Y-m-d')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاریخ پایان')
                    ->date('Y-m-d')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('remaining_days')
                    ->label('روزهای باقی‌مانده')
                    ->suffix(' روز')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status_badge')
                    ->label('وضعیت')
                    ->colors([
                        'success' => 'فعال',
                        'warning' => 'در انتظار شروع',
                        'danger' => 'منقضی شده',
                        'secondary' => 'غیرفعال',
                    ])
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership_type_id')
                    ->label('نوع عضویت')
                    ->options(MembershipType::pluck('name', 'id')),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت فعال')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
                
                Tables\Filters\Filter::make('expired')
                    ->label('منقضی شده')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now())),
                
                Tables\Filters\Filter::make('active')
                    ->label('فعال')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '>=', now())->where('start_date', '<=', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMemberships::route('/'),
            'create' => Pages\CreateMembership::route('/create'),
            'edit' => Pages\EditMembership::route('/{record}/edit'),
        ];
    }
} 