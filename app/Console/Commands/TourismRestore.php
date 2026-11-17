<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackup;
use Illuminate\Console\Command;

class TourismRestore extends Command
{
    protected $signature = 'tourism:restore {archive} {--connection= : Koneksi tujuan khusus yang sudah disiapkan} {--files-to= : Folder tujuan baru dan kosong} {--confirm-empty-target}';

    protected $description = 'Restore backup terverifikasi ke database dan folder tujuan kosong';

    public function handle(DatabaseBackup $backup): int
    {
        if (! $this->option('confirm-empty-target') || ! $this->option('connection') || ! $this->option('files-to')) {
            $this->error('Tentukan koneksi tujuan, folder kosong dan --confirm-empty-target. Tidak ada data diubah.');

            return self::FAILURE;
        }
        try {
            $backup->restoreToEmpty($this->argument('archive'), $this->option('connection'), $this->option('files-to'));
            $this->info('Restore selesai. Periksa aplikasi pada tujuan sebelum digunakan.');

            return self::SUCCESS;
        } catch (\Throwable) {
            $this->error('Restore ditolak/gagal. Periksa checksum, kecocokan driver, target kosong, izin dan binary database.');

            return self::FAILURE;
        }
    }
}
