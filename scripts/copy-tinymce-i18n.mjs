import { cpSync, existsSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = join(__dirname, '..');
const src = join(root, 'node_modules', 'tinymce-i18n', 'langs8');
const dest = join(root, 'public', 'vendor', 'tinymce-i18n', 'langs8');

if (!existsSync(src)) {
    console.warn('[copy-tinymce-i18n] Skip: node_modules/tinymce-i18n/langs8 not found (run npm install).');
    process.exit(0);
}

mkdirSync(dirname(dest), { recursive: true });
cpSync(src, dest, { recursive: true });
console.log('[copy-tinymce-i18n] Copied langs8 to public/vendor/tinymce-i18n/langs8');
