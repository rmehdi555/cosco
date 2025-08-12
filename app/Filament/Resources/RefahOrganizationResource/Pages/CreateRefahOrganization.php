<?php

namespace App\Filament\Resources\RefahOrganizationResource\Pages;

use App\Filament\Resources\RefahOrganizationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefahOrganization extends CreateRecord
{
    protected static string $resource = RefahOrganizationResource::class;

    public function getTitle(): string
    {
        return 'ایجاد سازمان رفاه جدید';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'سازمان رفاه با موفقیت ایجاد شد';
    }
}
