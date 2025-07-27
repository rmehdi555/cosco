<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $product = static::getModel()::create($data);

        // Handle images
        if (isset($data['images']) && is_array($data['images'])) {
            $hasMainImage = false;
            
            foreach ($data['images'] as $imageData) {
                if (isset($imageData['image_url'])) {
                    // If this is the first image and no main image is set, make it main
                    if (!$hasMainImage) {
                        $imageData['is_main'] = true;
                        $hasMainImage = true;
                    }
                    
                    $product->images()->create([
                        'image_url' => $imageData['image_url'],
                        'is_main' => $imageData['is_main'] ?? false,
                    ]);
                }
            }
        }

        return $product;
    }
} 