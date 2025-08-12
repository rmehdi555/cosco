<?php

namespace App\Filament\Resources\RefahUserResource\Pages;

use App\Filament\Resources\RefahUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefahUsers extends ListRecords
{
    protected static string $resource = RefahUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ثبت‌نام کاربر جدید'),
        ];
    }

    public function getTitle(): string
    {
        return 'کاربران رفاه';
    }
}
