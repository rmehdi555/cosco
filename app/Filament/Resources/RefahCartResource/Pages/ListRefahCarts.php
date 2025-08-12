<?php

namespace App\Filament\Resources\RefahCartResource\Pages;

use App\Filament\Resources\RefahCartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefahCarts extends ListRecords
{
    protected static string $resource = RefahCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ایجاد بسته رفاهی جدید'),
        ];
    }

    public function getTitle(): string
    {
        return 'بسته‌های رفاهی';
    }
}
