<?php

namespace Eduka\Nereus\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class PublishAssets extends Command
{
    protected $signature = 'eduka:publish-assets';

    protected $description = 'Copy assets from package to Laravel resources based on config.';

    public function handle()
    {
        $vendors = Config::get('eduka.system.assets-transfer-vendors', []);

        foreach ($vendors as $vendor) {
            $src = base_path("vendor/brunocfalcao/{$vendor}/resources/");
            $dst = resource_path("vendor/{$vendor}/");

            if (File::exists($src)) {
                $this->copyFiles($src, $dst);
            } else {
                $this->warn("Source directory does not exist: {$src}");
            }
        }

        $this->info('Assets have been published.');
    }

    private function copyFiles($src, $dst)
    {
        // Ensure the destination directory exists
        if (! File::exists($dst)) {
            File::makeDirectory($dst, 0755, true);
        }

        // Echo message before copying
        $this->line("Copying directory: {$src} -> {$dst}");

        if (File::copyDirectory($src, $dst)) {
            // Echo message after copying
            $this->line("Copied directory: {$src} -> {$dst}");
        } else {
            $this->error("Failed to copy: {$src} -> {$dst}");
        }
    }
}
