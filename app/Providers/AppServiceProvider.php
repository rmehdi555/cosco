<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load Swagger schemas
        if (class_exists('\App\Http\Resources\SwaggerSchemas')) {
            new \App\Http\Resources\SwaggerSchemas();
        }

        $this->ensureTinyMceI18nFiles();
        $this->registerLocalTinyMceLanguageAssets();
    }

    /**
     * Try to fetch tinymce-i18n langs8 from unpkg when missing (jsDelivr may be blocked).
     */
    protected function ensureTinyMceI18nFiles(): void
    {
        $version = config('filament-tinyeditor.version.language.version', '25.8.4');
        $dir = public_path('vendor/tinymce-i18n/langs8');
        $path = $dir.'/fa.min.js';

        if (is_file($path) && filesize($path) > 500) {
            return;
        }

        File::ensureDirectoryExists($dir);

        try {
            $url = "https://unpkg.com/tinymce-i18n@{$version}/langs8/fa.min.js";
            $response = Http::timeout(2)->get($url);
            if ($response->successful() && str_contains($response->body(), 'addI18n')) {
                File::put($path, $response->body());

                return;
            }
        } catch (\Throwable) {
            // try fallback file
        }

        $fallback = resource_path('tinymce-i18n-fallback/fa.min.js');
        if (is_file($fallback)) {
            File::copy($fallback, $path);
        }
    }

    /**
     * Override filament-tinyeditor language script URLs so local files are used instead of jsDelivr CDN.
     * Runs after the package registers assets (same package id = Filament merges/overrides).
     */
    protected function registerLocalTinyMceLanguageAssets(): void
    {
        $this->app->booted(function (): void {
            $assets = [];
            foreach (filament_tinymce_locale_codes() as $locale) {
                $rel = filament_tinymce_resolve_lang_public_relative($locale);
                $assets[] = Js::make(
                    'tinymce-lang-'.$locale,
                    tinymce_local_asset_url($rel)
                )->loadedOnRequest();
            }

            FilamentAsset::register($assets, package: 'amidesfahani/filament-tinyeditor');
        });
    }
}
