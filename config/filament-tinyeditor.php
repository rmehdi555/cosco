<?php

/**
 * Map every tinymce-i18n langs8/*.min.js under public to a local asset URL (no jsDelivr).
 */
function filament_tinymce_local_languages(): array
{
    $dir = public_path('vendor/tinymce-i18n/langs8');
    if (! is_dir($dir)) {
        return [];
    }

    $languages = [];
    foreach (glob($dir.'/*.min.js') ?: [] as $path) {
        $base = basename($path, '.min.js');
        $languages[$base] = tinymce_local_asset_url('vendor/tinymce-i18n/langs8/'.basename($path));
    }

    return $languages;
}

return [
    'version' => [
        'tiny' => '8.0.2',
        'language' => [
            // https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/
            'version' => '25.8.4',
            'package' => 'langs8',
        ],
        'licence_key' => env('TINY_LICENSE_KEY', 'no-api-key'),
    ],
    // Use TinyMCE from public/vendor/tinymce (published from composer tinymce/tinymce) instead of jsDelivr CDN.
    'provider' => 'vendor',

    /**
     * change darkMode: 'auto'|'force'|'class'|'media'|false|'custom'
     */
    'darkMode' => 'auto',

    /** cutsom */
    'skins' => [
        // oxide, oxide-dark, tinymce-5, tinymce-5-dark
        'ui' => 'oxide',

        // dark, default, document, tinymce-5, tinymce-5-dark, writer
        'content' => 'default',
    ],

    'profiles' => [
        'default' => [
            'plugins' => 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignleft aligncenter alignright | numlist bullist outdent indent | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,
        ],

        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | numlist bullist | link emoticons',
            'upload_directory' => null,
        ],

        'minimal' => [
            'plugins' => 'link wordcount',
            'toolbar' => 'bold italic link numlist bullist',
            'upload_directory' => null,
        ],

        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help',
            'upload_directory' => null,
        ],
    ],

    /**
     * Per-locale language pack URLs (overrides jsDelivr from the package).
     * Filled automatically from public/vendor/tinymce-i18n/langs8/*.min.js — run:
     * php artisan tinymce:sync-i18n
     * (or npm install, which runs scripts/copy-tinymce-i18n.mjs if you use the npm workflow).
     */
    'languages' => filament_tinymce_local_languages(),

    'extra' => [
        'toolbar' => [
            // 'fontsize' => '10px 12px 13px 14px 16px 18px 20px',
            // 'fontfamily' => 'Tahoma=tahoma,arial,helvetica,sans-serif;',
            // 'content_style' => 'body { font-family: "Tahoma", sans-serif; }',
        ],
    ],
];
