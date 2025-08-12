<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Hekmatinasser\Verta\Verta;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('ویرایش'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Grid::make(2)
                        ->schema([
                            Group::make()
                                ->schema([
                                    Section::make('اطلاعات سفارش')
                                        ->schema([
                                            TextEntry::make('id')
                                                ->label('شماره سفارش')
                                                ->badge()
                                                ->color('primary'),
                                            
                                            TextEntry::make('status')
                                                ->label('وضعیت سفارش')
                                                ->badge()
                                                ->color(fn ($state) => $state->getColor())
                                                ->formatStateUsing(fn ($state) => $state->getLabel()),
                                            
                                            TextEntry::make('payment_status')
                                                ->label('وضعیت پرداخت')
                                                ->badge()
                                                ->color(fn ($state) => $state->getColor())
                                                ->formatStateUsing(fn ($state) => $state->getLabel()),
                                            
                                            TextEntry::make('total_amount')
                                                ->label('مبلغ کل')
                                                ->money('IRR')
                                                ->size(TextEntry\TextEntrySize::Large)
                                                ->weight('bold'),
                                            
                                            TextEntry::make('created_at')
                                                ->label('تاریخ ثبت سفارش')
                                                ->formatStateUsing(fn ($state) => Verta::instance($state)->format('Y/n/j H:i:s'))
                                                ->icon('heroicon-m-calendar'),
                                            
                                            TextEntry::make('updated_at')
                                                ->label('آخرین بروزرسانی')
                                                ->formatStateUsing(fn ($state) => Verta::instance($state)->format('Y/n/j H:i:s'))
                                                ->icon('heroicon-m-clock'),
                                        ])
                                        ->columns(2),
                                ]),

                            Group::make()
                                ->schema([
                                    Section::make('اطلاعات مشتری')
                                        ->schema([
                                            TextEntry::make('user.first_name')
                                                ->label('نام')
                                                ->icon('heroicon-m-user'),
                                            
                                            TextEntry::make('user.last_name')
                                                ->label('نام خانوادگی')
                                                ->icon('heroicon-m-user'),
                                            
                                            TextEntry::make('user.email')
                                                ->label('ایمیل')
                                                ->icon('heroicon-m-envelope')
                                                ->copyable(),
                                            
                                            TextEntry::make('user.phone')
                                                ->label('شماره تلفن')
                                                ->icon('heroicon-m-phone')
                                                ->copyable(),
                                            
                                            TextEntry::make('user.national_code')
                                                ->label('کد ملی')
                                                ->icon('heroicon-m-identification')
                                                ->copyable(),
                                            
                                            TextEntry::make('user.created_at')
                                                ->label('تاریخ عضویت')
                                                ->formatStateUsing(fn ($state) => $state ? Verta::instance($state)->format('Y/n/j') : '-')
                                                ->icon('heroicon-m-calendar-days'),
                                        ])
                                        ->columns(2),
                                ]),
                        ]),
                ])
                ->from('lg'),

                Section::make('آدرس ارسال')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('shippingAddress.province.title_fa')
                                    ->label('استان')
                                    ->icon('heroicon-m-map-pin'),
                                
                                TextEntry::make('shippingAddress.city.title_fa')
                                    ->label('شهر')
                                    ->icon('heroicon-m-building-office'),
                                
                                TextEntry::make('shippingAddress.postal_code')
                                    ->label('کد پستی')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable(),
                            ]),
                        
                        TextEntry::make('shippingAddress.address')
                            ->label('آدرس کامل')
                            ->icon('heroicon-m-map')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('آیتم‌های سفارش')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->label('')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->label('نام محصول')
                                            ->weight('bold')
                                            ->icon('heroicon-m-cube'),
                                        
                                        TextEntry::make('product.brand.name')
                                            ->label('برند')
                                            ->icon('heroicon-m-tag'),
                                        
                                        TextEntry::make('quantity')
                                            ->label('تعداد')
                                            ->badge()
                                            ->color('info'),
                                        
                                        TextEntry::make('price')
                                            ->label('قیمت واحد')
                                            ->money('IRR'),
                                        
                                        TextEntry::make('total_price')
                                            ->label('قیمت کل')
                                            ->money('IRR')
                                            ->weight('bold')
                                            ->color('success'),
                                        
                                        TextEntry::make('product.stock')
                                            ->label('موجودی فعلی')
                                            ->badge()
                                            ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                                    ]),
                            ])
                            ->columns(1),
                    ])
                    ->collapsed(false),

                Section::make('خلاصه مالی')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('تعداد کل آیتم‌ها')
                                    ->formatStateUsing(fn ($state, $record) => $record->orderItems->sum('quantity'))
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('id')
                                    ->label('مجموع قیمت آیتم‌ها')
                                    ->formatStateUsing(function ($state, $record) {
                                        $total = $record->orderItems->sum('total_price');
                                        return number_format($total) . ' ریال';
                                    })
                                    ->weight('bold'),
                                
                                TextEntry::make('total_amount')
                                    ->label('مبلغ نهایی')
                                    ->money('IRR')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color('success'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
} 