<?php

namespace App\Forms\Components;

use AmidEsfahani\FilamentTinyEditor\TinyEditor as BaseTinyEditor;

/**
 * Uses local tinymce-i18n files for {@see BaseTinyEditor::getLanguageURL()} (TinyMCE `language_url` in blade).
 * The vendor implementation always points to jsDelivr; that bypasses Filament asset overrides.
 */
class TinyEditor extends BaseTinyEditor
{
    public function getLanguageURL($lang): string
    {
        $id = $this->getLanguageId();

        if ($id === 'tinymce') {
            return tinymce_local_asset_url(filament_tinymce_resolve_lang_public_relative('en'));
        }

        $key = str_replace('tinymce-lang-', '', $id);

        return tinymce_local_asset_url(filament_tinymce_resolve_lang_public_relative($key));
    }
}
