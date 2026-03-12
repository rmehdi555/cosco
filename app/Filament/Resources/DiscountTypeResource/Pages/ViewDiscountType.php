<?php

namespace App\Filament\Resources\DiscountTypeResource\Pages;

use App\Filament\Resources\DiscountTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Schemas\Schema;

class ViewDiscountType extends ViewRecord
{
    protected static string $resource = DiscountTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('ویرایش'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Infolists\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Infolists\Components\TextEntry::make('productCategory.name')
                            ->label('دسته‌بندی محصول'),
                        Infolists\Components\TextEntry::make('title')
                            ->label('عنوان'),
                        Infolists\Components\TextEntry::make('footer')
                            ->label('پاورقی'),
                        Infolists\Components\TextEntry::make('background_color_up')
                            ->label('رنگ پس‌زمینه بالا')
                            ->color(fn (string $state): string => $state),
                        Infolists\Components\TextEntry::make('background_color_down')
                            ->label('رنگ پس‌زمینه پایین')
                            ->color(fn (string $state): string => $state),
                        Infolists\Components\ImageEntry::make('image_url')
                            ->label('تصویر')
                            ->disk('public'),
                        Infolists\Components\TextEntry::make('link')
                            ->label('لینک'),
                        Infolists\Components\IconEntry::make('target')
                            ->label('باز شدن در تب جدید')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_show')
                            ->label('نمایش')
                            ->boolean(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('اطلاعات تبلیغات')
                    ->schema([
                        Infolists\Components\TextEntry::make('ads_title')
                            ->label('عنوان تبلیغات'),
                        Infolists\Components\TextEntry::make('ads_footer')
                            ->label('پاورقی تبلیغات'),
                        Infolists\Components\TextEntry::make('ads_background_color_up')
                            ->label('رنگ پس‌زمینه تبلیغات بالا')
                            ->color(fn (string $state): string => $state),
                        Infolists\Components\TextEntry::make('ads_background_color_down')
                            ->label('رنگ پس‌زمینه تبلیغات پایین')
                            ->color(fn (string $state): string => $state),
                        Infolists\Components\ImageEntry::make('ads_image_url')
                            ->label('تصویر تبلیغات')
                            ->disk('public'),
                        Infolists\Components\TextEntry::make('ads_link')
                            ->label('لینک تبلیغات'),
                        Infolists\Components\IconEntry::make('ads_target')
                            ->label('باز شدن تبلیغات در تب جدید')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
