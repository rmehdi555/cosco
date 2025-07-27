<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Handle images
        if (isset($data['images']) && is_array($data['images'])) {
            // First, remove all existing images
            $record->images()->delete();
            
            $hasMainImage = false;
            
            foreach ($data['images'] as $imageData) {
                if (isset($imageData['image_url'])) {
                    // If this is the first image and no main image is set, make it main
                    if (!$hasMainImage) {
                        $imageData['is_main'] = true;
                        $hasMainImage = true;
                    }
                    
                    $record->images()->create([
                        'image_url' => $imageData['image_url'],
                        'is_main' => $imageData['is_main'] ?? false,
                    ]);
                }
            }
        }

        return $record;
    }
} 