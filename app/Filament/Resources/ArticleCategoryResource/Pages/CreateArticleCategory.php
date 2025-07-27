<?php

namespace App\Filament\Resources\ArticleCategoryResource\Pages;

use App\Filament\Resources\ArticleCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $data;
    }
}
