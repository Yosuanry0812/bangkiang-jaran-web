<?php

namespace App\Console\Commands;

use Database\Seeders\BookingSnapshotSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportSnapshot extends Command
{
    protected $signature = 'db:snapshot-export';

    protected $description = 'Ekspor data aplikasi ke database/seeders/data/booking-snapshot.json (tanpa password/token/IP)';

    public function handle(): int
    {
        $out = [];
        foreach (BookingSnapshotSeeder::TABLES as $table) {
            $null = BookingSnapshotSeeder::SCRUB[$table] ?? [];
            $drop = BookingSnapshotSeeder::DROP[$table] ?? [];
            $out[$table] = DB::table($table)->get()->map(function ($row) use ($null, $drop) {
                $row = (array) $row;
                foreach ($null as $col) {
                    if (array_key_exists($col, $row)) {
                        $row[$col] = null;
                    }
                }

                return array_diff_key($row, array_flip($drop));
            })->all();
            $this->line($table.': '.count($out[$table]));
        }

        file_put_contents(
            database_path('seeders/data/booking-snapshot.json'),
            json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );

        return self::SUCCESS;
    }
}
