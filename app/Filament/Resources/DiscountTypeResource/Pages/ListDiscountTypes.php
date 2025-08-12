<?php

namespace App\Filament\Resources\DiscountTypeResource\Pages;

use App\Filament\Resources\DiscountTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscountTypes extends ListRecords
{
    protected static string $resource = DiscountTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ایجاد طرح جدید'),
        ];
    }
}
