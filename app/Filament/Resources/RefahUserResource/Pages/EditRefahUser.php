<?php

namespace App\Filament\Resources\RefahUserResource\Pages;

use App\Filament\Resources\RefahUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefahUser extends EditRecord
{
    protected static string $resource = RefahUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTitle(): string
    {
        return 'ویرایش کاربر رفاه';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'اطلاعات کاربر با موفقیت بروزرسانی شد';
    }
}
