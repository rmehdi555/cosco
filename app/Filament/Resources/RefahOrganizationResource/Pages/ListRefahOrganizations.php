<?php

namespace App\Filament\Resources\RefahOrganizationResource\Pages;

use App\Filament\Resources\RefahOrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefahOrganizations extends ListRecords
{
    protected static string $resource = RefahOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ایجاد سازمان رفاه جدید'),
        ];
    }

    public function getTitle(): string
    {
        return 'سازمان‌های رفاه';
    }
}
