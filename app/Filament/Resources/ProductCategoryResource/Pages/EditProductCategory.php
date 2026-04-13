<?php

namespace App\Filament\Resources\ProductCategoryResource\Pages;

use App\Filament\Resources\ProductCategoryResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EditProductCategory extends EditRecord
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload_products')
                ->label('آپلود محصولات')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
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
                    /** @var ProductCategory $parentCategory */
                    $parentCategory = $this->record;

                    $uploadedPath = $data['file'] ?? null;
                    if (!is_string($uploadedPath) || $uploadedPath === '') {
                        Notification::make()->title('فایل معتبر نیست.')->danger()->send();
                        return;
                    }

                    // Filament may return a relative path (e.g. "imports/file.xlsx")
                    // or an absolute path inside the container (e.g. "/var/www/storage/app/imports/file.xlsx").
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

                    $defaultBrandId = Brand::query()->value('id');
                    if (!$defaultBrandId) {
                        Notification::make()
                            ->title('هیچ برندی وجود ندارد')
                            ->body('قبل از آپلود، حداقل یک برند بسازید یا در اکسل ستون brand_id را قرار دهید.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $createdProducts = 0;
                    $skippedRows = 0;
                    $createdCategories = 0;
                    $createdImages = 0;

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

                    $required = ['name', 'category', 'code'];
                    foreach ($required as $req) {
                        if (!array_key_exists($req, $headers)) {
                            Notification::make()
                                ->title('ستون‌های لازم در اکسل وجود ندارد')
                                ->body('ستون‌های لازم: name, category, code')
                                ->danger()
                                ->send();
                            return;
                        }
                    }

                    $imageHeaderKeys = array_values(array_filter(array_keys($headers), fn (string $h) => preg_match('/^image\\d+$/', $h) === 1));
                    sort($imageHeaderKeys);

                    DB::beginTransaction();
                    try {
                        foreach ($rows as $row) {
                            $name = trim((string) ($row[$headers['name']] ?? ''));
                            $categoryName = trim((string) ($row[$headers['category']] ?? ''));
                            $code = trim((string) ($row[$headers['code']] ?? ''));

                            if ($name === '' || $categoryName === '' || $code === '') {
                                $skippedRows++;
                                continue;
                            }

                            if (Product::query()->where('code', $code)->exists()) {
                                $skippedRows++;
                                continue;
                            }

                            $childCategory = ProductCategory::query()
                                ->where('parent_id', $parentCategory->id)
                                ->where('name', $categoryName)
                                ->first();

                            if (!$childCategory) {
                                $baseSlug = Str::slug($categoryName) ?: ('cat-' . Str::random(6));
                                $slug = $baseSlug;
                                $i = 1;
                                while (ProductCategory::query()->where('slug', $slug)->exists()) {
                                    $slug = $baseSlug . '-' . $i;
                                    $i++;
                                }

                                $childCategory = ProductCategory::query()->create([
                                    'parent_id' => $parentCategory->id,
                                    'name' => $categoryName,
                                    'slug' => $slug,
                                    'image_url' => $parentCategory->image_url ?: 'product-categories/default.png',
                                    'description' => $parentCategory->description ?: 'Imported from Excel',
                                    'is_active' => true,
                                ]);
                                $createdCategories++;
                            }

                            $productData = [
                                'product_category_id' => $childCategory->id,
                                'name' => $name,
                                'code' => $code,
                            ];

                            // Optional columns (only if they exist in the sheet)
                            $productColumns = [
                                'brand_id',
                                'slug',
                                'description',
                                'body',
                                'price',
                                'stock',
                                'is_active',
                                'is_featured',
                                'is_online_only',
                            ];

                            foreach ($productColumns as $colName) {
                                if (!isset($headers[$colName])) {
                                    continue;
                                }

                                $raw = $row[$headers[$colName]] ?? null;

                                $productData[$colName] = match ($colName) {
                                    'brand_id' => (int) $raw,
                                    'price' => is_numeric($raw) ? (float) $raw : 0,
                                    'stock' => is_numeric($raw) ? (int) $raw : 0,
                                    'is_active', 'is_featured', 'is_online_only' => (bool) $raw,
                                    default => is_string($raw) ? $raw : ($raw === null ? null : (string) $raw),
                                };
                            }

                            // Defaults for required fields
                            if (empty($productData['brand_id']) || !Brand::query()->whereKey($productData['brand_id'])->exists()) {
                                $productData['brand_id'] = $defaultBrandId;
                            }

                            if (!array_key_exists('price', $productData)) {
                                $productData['price'] = 0;
                            }
                            if (!array_key_exists('stock', $productData)) {
                                $productData['stock'] = 0;
                            }
                            if (!array_key_exists('is_active', $productData)) {
                                $productData['is_active'] = true;
                            }
                            if (!array_key_exists('is_featured', $productData)) {
                                $productData['is_featured'] = false;
                            }
                            if (!array_key_exists('is_online_only', $productData)) {
                                $productData['is_online_only'] = false;
                            }

                            if (empty($productData['slug'])) {
                                $productData['slug'] = Str::slug($name . '-' . $code);
                            }
                            if ($productData['slug'] === '') {
                                $productData['slug'] = 'product-' . Str::random(10);
                            }

                            // Ensure slug uniqueness
                            $baseProductSlug = $productData['slug'];
                            $j = 1;
                            while (Product::query()->where('slug', $productData['slug'])->exists()) {
                                $productData['slug'] = $baseProductSlug . '-' . $j;
                                $j++;
                            }

                            $product = Product::query()->create($productData);
                            $createdProducts++;

                            $isMainSet = false;
                            foreach ($imageHeaderKeys as $imgKey) {
                                $raw = trim((string) ($row[$headers[$imgKey]] ?? ''));
                                if ($raw === '') {
                                    continue;
                                }

                                $normalized = ltrim($raw, '/\\');
                                $imageUrl = 'product-images/category-' .$parentCategory->id . '/' . $normalized;

                                ProductImage::query()->create([
                                    'product_id' => $product->id,
                                    'image_url' => $imageUrl,
                                    'is_main' => $isMainSet ? false : true,
                                ]);

                                $isMainSet = true;
                                $createdImages++;
                            }
                        }

                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title('آپلود ناموفق بود')
                            ->body('در هنگام پردازش فایل خطا رخ داد.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('آپلود انجام شد')
                        ->body("محصول ایجاد شد: {$createdProducts} | دسته‌بندی ساخته شد: {$createdCategories} | تصویر ایجاد شد: {$createdImages} | سطر رد شد: {$skippedRows}")
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $data;
    }
} 