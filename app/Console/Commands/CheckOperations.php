<?php

namespace App\Console\Commands;

use App\Services\OperationalReadiness;
use Illuminate\Console\Command;

class CheckOperations extends Command
{
    protected $signature = 'operations:check {--json : Hasil yang dapat dibaca pengawas proses} {--smtp : Uji koneksi dan autentikasi tanpa mengirim email}';

    protected $description = 'Periksa kesiapan schema, storage dan antrean tanpa mengubah database';

    public function handle(OperationalReadiness $readiness): int
    {
        $result = $readiness->inspect((bool) $this->option('smtp'));
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
        } else {
            $this->line('Database terhubung: '.($result['database']['connected'] ? 'ya' : 'tidak'));
            $this->line('Schema belum tersedia: '.(implode(', ', $result['missing_schema']) ?: 'tidak ada'));
            $this->line('Storage privat siap: '.($result['private_storage_writable'] ? 'ya' : 'tidak'));
            $this->line('Antrean database dipilih: '.($result['transactional_queue_database'] ? 'ya' : 'tidak'));
            $this->line('Bukti publik tersisa: '.($result['legacy_public_proofs'] ?? 'belum dapat diperiksa'));
            if (isset($result['smtp'])) {
                $this->line('SMTP: '.$result['smtp']['status'].'; tidak mengirim email.');
            }
        }

        return $result['ready'] && (! isset($result['smtp']) || $result['smtp']['authenticated']) ? self::SUCCESS : self::FAILURE;
    }
}
