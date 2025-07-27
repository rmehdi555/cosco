<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\File;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\File as LaravelFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $file = File::find($data['file_id']);
        if ($file?->path)
            $data['image_name'] = str_replace(config('app.url') . '/storage/', '', $file?->path);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
