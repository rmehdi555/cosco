<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class SyncTinyMceI18nCommand extends Command
{
    protected $signature = 'tinymce:sync-i18n {--version=25.8.4 : tinymce-i18n npm version}';

    protected $description = 'Download tinymce-i18n from npm registry and copy langs8 to public/vendor (offline Filament TinyMCE UI languages)';

    public function handle(): int
    {
        $version = (string) $this->option('version');
        $url = "https://registry.npmjs.org/tinymce-i18n/-/tinymce-i18n-{$version}.tgz";
        $tgz = storage_path("app/tinymce-i18n-{$version}.tgz");
        $work = storage_path('app/tinymce-i18n-extract');
        $dest = public_path('vendor/tinymce-i18n/langs8');

        $this->info("Fetching {$url} …");

        $response = Http::timeout(120)
            ->withOptions(['sink' => $tgz])
            ->get($url);

        if (! $response->successful()) {
            $this->error('Download failed: HTTP '.$response->status());

            return self::FAILURE;
        }

        if (! is_file($tgz) || filesize($tgz) < 1000) {
            $this->error('Downloaded archive is missing or too small.');

            return self::FAILURE;
        }

        File::deleteDirectory($work);
        File::ensureDirectoryExists($work);

        $tar = new Process(['tar', '-xzf', $tgz, '-C', $work]);
        $tar->setTimeout(120);
        $tar->run();

        if (! $tar->isSuccessful()) {
            $this->error('tar failed: '.$tar->getErrorOutput());

            return self::FAILURE;
        }

        $langs8 = $work.'/package/langs8';
        if (! is_dir($langs8)) {
            $this->error('Expected directory package/langs8 not found in archive.');

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($dest));
        File::deleteDirectory($dest);
        File::copyDirectory($langs8, $dest);

        @unlink($tgz);
        File::deleteDirectory($work);

        $count = count(File::glob($dest.'/*.min.js'));
        $this->info("Copied {$count} language files to public/vendor/tinymce-i18n/langs8");

        return self::SUCCESS;
    }
}
