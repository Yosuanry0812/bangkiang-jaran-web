<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackup;
use Illuminate\Console\Command;

class TourismBackup extends Command
{
    protected $signature = 'tourism:backup {--connection=} {--verify= : Verifikasi arsip yang sudah ada}';

    protected $description = 'Backup database dan berkas galeri/bukti privat dengan checksum';

    public function handle(DatabaseBackup $backup): int
    {
        try {
            if ($this->option('verify')) {
                $backup->verify($this->option('verify'));
                $this->info('Checksum backup valid.');
            } else {
                $path = $backup->create($this->option('connection'));
                $this->info('Backup tersimpan di direktori privat: '.basename($path));
            }

            return self::SUCCESS;
        } catch (\Throwable) {
            $this->error('Backup/verifikasi gagal. Periksa koneksi, ruang penyimpanan, izin dan binary database.');

            return self::FAILURE;
        }
    }
}
