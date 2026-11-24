<?php

namespace App\Console\Commands;

use App\Http\Controllers\MediaVariantController;
use Illuminate\Console\Command;

class WarmMediaVariants extends Command
{
    protected $signature = 'media:warm {paths* : Path foto di disk public, mis. galeri/foto.jpg}';

    protected $description = 'Buat versi ringan (WebP) foto galeri di latar belakang';

    public function handle(): int
    {
        foreach ($this->argument('paths') as $path) {
            if (preg_match('#^galeri/[A-Za-z0-9._-]+$#', $path)) {
                MediaVariantController::warm($path);
            }
        }

        return self::SUCCESS;
    }
}
