<?php

namespace App\Filament\Resources\RefahOrganizationResource\Pages;

use App\Filament\Resources\RefahOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefahOrganization extends EditRecord
{
    protected static string $resource = RefahOrganizationResource::class;

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
        return 'ویرایش سازمان رفاه';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'سازمان رفاه با موفقیت بروزرسانی شد';
    }
}
