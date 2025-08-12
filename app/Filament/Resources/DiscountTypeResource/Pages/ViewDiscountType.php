<?php

namespace App\Filament\Resources\DiscountTypeResource\Pages;

use App\Filament\Resources\DiscountTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscountType extends ViewRecord
{
    protected static string $resource = DiscountTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('ویرایش'),
        ];
    }
}
