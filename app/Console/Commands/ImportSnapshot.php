<?php

namespace App\Console\Commands;

use Database\Seeders\BookingSnapshotSeeder;
use Illuminate\Console\Command;

class ImportSnapshot extends Command
{
    protected $signature = 'db:snapshot-import';

    protected $description = 'Impor database/seeders/data/booking-snapshot.json (MENGGANTI isi tabel aplikasi)';

    public function handle(): int
    {
        $seeder = new BookingSnapshotSeeder();
        $seeder->setContainer($this->laravel)->setCommand($this);
        $seeder->run();

        return self::SUCCESS;
    }
}
