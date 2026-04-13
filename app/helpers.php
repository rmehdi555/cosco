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

if (! function_exists('filament_tinymce_locale_codes')) {
    /**
     * Locale codes registered by amidesfahani/filament-tinyeditor (Tiny::$languages).
     * Must stay in sync so config never falls back to jsDelivr CDN.
     */
    function filament_tinymce_locale_codes(): array
    {
        return [
            'ar', 'az', 'bg_BG', 'bn_BD', 'ca', 'cs', 'cy', 'da', 'de', 'dv', 'el', 'eo', 'es', 'et', 'es_MX', 'eu',
            'fa', 'fi', 'fr_FR', 'ga', 'gl', 'he_IL', 'hr', 'hu_HU', 'hy', 'id', 'is_IS', 'it', 'ja', 'kab', 'kk',
            'ko_KR', 'ku', 'lt', 'lv', 'nb_NO', 'nl', 'nl_BE', 'oc', 'pl', 'pt_BR', 'ro', 'ru', 'sk', 'sl_SI', 'sq',
            'sr', 'sv_SE', 'ta', 'tg', 'th_TH', 'tr', 'ug', 'uk', 'vi',
        ];
    }
}

if (! function_exists('filament_tinymce_resolve_lang_public_relative')) {
    /**
     * Public path under /public for the best available langs8 file (never CDN).
     * Preference: exact locale → en → fa (fa is always ensured by AppServiceProvider).
     */
    function filament_tinymce_resolve_lang_public_relative(string $locale): string
    {
        $dir = public_path('vendor/tinymce-i18n/langs8');
        foreach ([$locale.'.min.js', 'en.min.js', 'fa.min.js'] as $file) {
            if (is_file($dir.DIRECTORY_SEPARATOR.$file)) {
                return 'vendor/tinymce-i18n/langs8/'.$file;
            }
        }

        return 'vendor/tinymce-i18n/langs8/fa.min.js';
    }
}

if (! function_exists('filament_tinymce_local_languages')) {
    /**
     * Override every TinyMCE UI language asset with a local URL (blocks jsDelivr completely).
     */
    function filament_tinymce_local_languages(): array
    {
        $languages = [];
        foreach (filament_tinymce_locale_codes() as $locale) {
            $rel = filament_tinymce_resolve_lang_public_relative($locale);
            $languages[$locale] = tinymce_local_asset_url($rel);
        }

        return $languages;
    }
}
