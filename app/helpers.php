<?php

if (! function_exists('tinymce_local_asset_url')) {
    /**
     * Absolute URL for a file under /public without using asset() / UrlGenerator.
     * Safe for php artisan (console) where Request may be null.
     */
    function tinymce_local_asset_url(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');
        $base = rtrim((string) config('app.url', ''), '/');

        if ($base === '') {
            return '/'.$relativePath;
        }

        return $base.'/'.$relativePath;
    }
}
