<?php

namespace App\Filament\Resources\RefahUserResource\Pages;

use App\Filament\Resources\RefahUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefahUser extends CreateRecord
{
    protected static string $resource = RefahUserResource::class;

    public function getTitle(): string
    {
        return 'ثبت‌نام کاربر رفاه جدید';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'کاربر رفاه با موفقیت ثبت‌نام شد';
    }
}
