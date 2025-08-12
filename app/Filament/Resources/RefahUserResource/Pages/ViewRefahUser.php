<?php

namespace App\Filament\Resources\RefahUserResource\Pages;

use App\Filament\Resources\RefahUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Hekmatinasser\Verta\Verta;

class ViewRefahUser extends ViewRecord
{
    protected static string $resource = RefahUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('ویرایش'),
        ];
    }

    public function getTitle(): string
    {
        return 'مشاهده کاربر رفاه';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('اطلاعات شخصی')
                    ->schema([
                        TextEntry::make('id')
                            ->label('شناسه'),

                        TextEntry::make('full_name')
                            ->label('نام و نام خانوادگی')
                            ->state(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('national_code')
                            ->label('کد ملی')
                            ->copyable()
                            ->copyMessage('کد ملی کپی شد'),

                        TextEntry::make('code')
                            ->label('کد ')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('کد کپی شد'),

                        TextEntry::make('birth_date')
                            ->label('تاریخ تولد')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return Verta::instance($state)->format('Y/n/j');
                                }
                                return '-';
                            }),

                        TextEntry::make('gender')
                            ->label('جنسیت')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'male' => 'مرد',
                                'female' => 'زن',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'success',
                                default => 'gray',
                            }),

                        TextEntry::make('number_of_family_members')
                            ->label('تعداد اعضای خانواده')
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(2),

                Section::make('اطلاعات تماس و آدرس')
                    ->schema([
                        TextEntry::make('cell_phone')
                            ->label('شماره موبایل')
                            ->copyable()
                            ->copyMessage('شماره موبایل کپی شد'),

                        TextEntry::make('phone')
                            ->label('تلفن ثابت')
                            ->copyable()
                            ->copyMessage('تلفن ثابت کپی شد'),

                        TextEntry::make('country.title_fa')
                            ->label('کشور'),

                        TextEntry::make('province.title_fa')
                            ->label('استان'),

                        TextEntry::make('city.title_fa')
                            ->label('شهر'),

                        TextEntry::make('postal_code')
                            ->label('کد پستی')
                            ->copyable()
                            ->copyMessage('کد پستی کپی شد'),

                        TextEntry::make('address')
                            ->label('آدرس کامل')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('اطلاعات شغلی و مالی')
                    ->schema([
                        TextEntry::make('job')
                            ->label('شغل'),

                        TextEntry::make('income')
                            ->label('درآمد')
                            ->money('IRR')
                            ->weight('bold')
                            ->color('success'),
                    ])
                    ->columns(2),

                Section::make('اطلاعات بسته رفاهی')
                    ->schema([
                        TextEntry::make('refahOrganization.title')
                            ->label('سازمان رفاه')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('refahCart.title')
                            ->label('بسته رفاهی انتخابی')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('refahCart.price')
                            ->label('قیمت بسته')
                            ->money('IRR')
                            ->weight('bold')
                            ->color('success'),

                        TextEntry::make('how_to_receive')
                            ->label('نحوه دریافت')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'in_person' => 'حضوری',
                                'mail_to_address' => 'ارسال به آدرس',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'in_person' => 'info',
                                'mail_to_address' => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('payment_method')
                            ->label('روش پرداخت')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'نقدی',
                                'card' => 'کارت',
                                'online' => 'آنلاین',
                                'installment' => 'اقساطی',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'cash' => 'success',
                                'card' => 'info',
                                'online' => 'warning',
                                'installment' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('اطلاعات سیستم')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاریخ ثبت‌نام')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return Verta::instance($state)->format('Y/n/j H:i:s');
                                }
                                return '-';
                            }),

                        TextEntry::make('updated_at')
                            ->label('آخرین بروزرسانی')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return Verta::instance($state)->format('Y/n/j H:i:s');
                                }
                                return '-';
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
