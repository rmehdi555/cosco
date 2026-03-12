<?php

namespace App\Filament\Resources\RefahCartResource\Pages;

use App\Filament\Resources\RefahCartResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class ViewRefahCart extends ViewRecord
{
    protected static string $resource = RefahCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('ویرایش'),
        ];
    }

    public function getTitle(): string
    {
        return 'مشاهده بسته رفاهی';
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات بسته رفاهی')
                    ->schema([
                        TextEntry::make('id')
                            ->label('شناسه'),

                        TextEntry::make('title')
                            ->label('عنوان بسته')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('description')
                            ->label('توضیحات')
                            ->prose()
                            ->columnSpanFull(),

                        TextEntry::make('price')
                            ->label('قیمت')
                            ->money('IRR')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),

                        IconEntry::make('is_active')
                            ->label('وضعیت')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        TextEntry::make('refah_users_count')
                            ->label('تعداد انتخاب‌ها')
                            ->state(fn ($record) => $record->refahUsers()->count())
                            ->badge()
                            ->color('info'),

                        TextEntry::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->dateTime('Y/m/d H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('آخرین بروزرسانی')
                            ->dateTime('Y/m/d H:i:s'),
                    ])
                    ->columns(2),
            ]);
    }
}
