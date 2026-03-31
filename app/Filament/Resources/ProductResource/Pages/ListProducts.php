<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('upload_products_update')
                ->label('آپلود فایل ویرایش محصولات')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    FileUpload::make('file')
                        ->label('فایل اکسل')
                        ->disk('local')
                        ->directory('imports')
                        ->preserveFilenames()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $uploadedPath = $data['file'] ?? null;
                    if (!is_string($uploadedPath) || $uploadedPath === '') {
                        Notification::make()->title('فایل معتبر نیست.')->danger()->send();
                        return;
                    }

                    $fullPath = null;
                    if (is_file($uploadedPath)) {
                        $fullPath = $uploadedPath;
                    } elseif (Storage::disk('local')->exists($uploadedPath)) {
                        $fullPath = Storage::disk('local')->path($uploadedPath);
                    } elseif (Storage::disk('local')->exists('imports/' . ltrim($uploadedPath, '/\\'))) {
                        $fullPath = Storage::disk('local')->path('imports/' . ltrim($uploadedPath, '/\\'));
                    }

                    if (!$fullPath || !is_file($fullPath)) {
                        Notification::make()->title('فایل روی سرور پیدا نشد.')->danger()->send();
                        return;
                    }

                    try {
                        $spreadsheet = IOFactory::load($fullPath);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray(null, true, true, true);
                    } catch (\Throwable $e) {
                        Notification::make()->title('خواندن فایل اکسل ناموفق بود.')->danger()->send();
                        return;
                    }

                    if (count($rows) < 2) {
                        Notification::make()->title('فایل اکسل خالی است.')->warning()->send();
                        return;
                    }

                    $headerRow = array_shift($rows);
                    $headers = [];
                    foreach ($headerRow as $col => $header) {
                        $key = is_string($header) ? trim(mb_strtolower($header)) : '';
                        if ($key !== '') {
                            $headers[$key] = $col;
                        }
                    }

                    if (!isset($headers['code'])) {
                        Notification::make()
                            ->title('ستون code وجود ندارد')
                            ->body('برای پیدا کردن محصول باید ستون code در اکسل موجود باشد.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $updatable = [
                        'name',
                        'slug',
                        'description',
                        'body',
                        'price',
                        'stock',
                        'is_active',
                        'is_featured',
                        'is_online_only',
                    ];

                    $updated = 0;
                    $skipped = 0;
                    $notFound = 0;

                    DB::beginTransaction();
                    try {
                        foreach ($rows as $row) {
                            $code = trim((string) ($row[$headers['code']] ?? ''));
                            if ($code === '') {
                                $skipped++;
                                continue;
                            }

                            /** @var Product|null $product */
                            $product = Product::query()->where('code', $code)->first();
                            if (!$product) {
                                $notFound++;
                                continue;
                            }

                            $changes = [];
                            foreach ($updatable as $field) {
                                if (!isset($headers[$field])) {
                                    continue;
                                }

                                $value = $row[$headers[$field]] ?? null;
                                if ($value === null) {
                                    continue; // only update when not null
                                }

                                $changes[$field] = match ($field) {
                                    'price' => is_numeric($value) ? (float) $value : $product->price,
                                    'stock' => is_numeric($value) ? (int) $value : $product->stock,
                                    'is_active', 'is_featured', 'is_online_only' => (bool) $value,
                                    default => is_string($value) ? trim($value) : (string) $value,
                                };
                            }

                            if ($changes === []) {
                                $skipped++;
                                continue;
                            }

                            $product->update($changes);
                            $updated++;
                        }

                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title('آپلود ناموفق بود')
                            ->body('در هنگام آپدیت محصولات خطا رخ داد.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('آپدیت محصولات انجام شد')
                        ->body("آپدیت شد: {$updated} | پیدا نشد: {$notFound} | رد شد: {$skipped}")
                        ->success()
                        ->send();
                }),
        ];
    }
} 