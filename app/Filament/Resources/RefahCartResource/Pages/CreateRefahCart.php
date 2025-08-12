<?php

namespace App\Filament\Resources\RefahCartResource\Pages;

use App\Filament\Resources\RefahCartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefahCart extends CreateRecord
{
    protected static string $resource = RefahCartResource::class;

    public function getTitle(): string
    {
        return 'ایجاد بسته رفاهی جدید';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'بسته رفاهی با موفقیت ایجاد شد';
    }
}
