<?php

namespace App\Filament\Resources\ArticleCategoryResource\Pages;

use App\Filament\Resources\ArticleCategoryResource;
use App\Models\File;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\File as LaravelFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $imagePath = Storage::disk('public')->path($data['image_name']);

        $file = File::create([
            'caption' => 'category image: ' . $data['title'],
            'path' => config('app.url') . '/storage/' . $data['image_name'],
            'extensions' => LaravelFile::mimeType($imagePath),
            'hash' => Hash::make($imagePath),
            'original_name' => $data['title'],
            'size' => LaravelFile::size($imagePath),
            'user_id' => auth()->id(),
            'file_category_id' => 2,
        ]);
        $data['file_id'] = $file->id;
        $data['user_id'] = auth()->id();
        return $data;
    }
}
