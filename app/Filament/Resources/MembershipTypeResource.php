<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipTypeResource\Pages;
use App\Models\MembershipType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembershipTypeResource extends Resource
{
    protected static ?string $model = MembershipType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'مدیریت عضویت';

    protected static ?string $navigationLabel = 'انواع عضویت';

    protected static ?string $modelLabel = 'نوع عضویت';

    protected static ?string $pluralModelLabel = 'انواع عضویت';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات نوع عضویت')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('قیمت (ریال)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(1000),
                        
                        Forms\Components\TextInput::make('day_cycle')
                            ->label('دوره (روز)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->step(1),
                        
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
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('قیمت')
                    ->money('IRR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('day_cycle')
                    ->label('دوره')
                    ->suffix(' روز')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('memberships_count')
                    ->label('تعداد اعضا')
                    ->counts('memberships')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListMembershipTypes::route('/'),
            'create' => Pages\CreateMembershipType::route('/create'),
            'edit' => Pages\EditMembershipType::route('/{record}/edit'),
        ];
    }
} 