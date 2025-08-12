<?php

namespace App\Filament\Resources\RefahCartResource\Pages;

use App\Filament\Resources\RefahCartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefahCart extends EditRecord
{
    protected static string $resource = RefahCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('مشاهده'),
            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }

    public function getTitle(): string
    {
        return 'ویرایش بسته رفاهی';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'بسته رفاهی با موفقیت بروزرسانی شد';
    }
}
