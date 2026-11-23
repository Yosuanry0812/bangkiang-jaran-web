<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Menyamakan data users, tiket, pemesanan, detail_pemesanan, dan pembayaran
 * dengan snapshot di database/seeders/data/booking-snapshot.json.
 *
 * PERINGATAN: data pada kelima tabel itu DIGANTI total (bukan digabung).
 * Snapshot tidak berisi password/token/google_id. Semua akun diberi password demo
 * dari env SNAPSHOT_USER_PASSWORD (default: Password123). Ganti sebelum dipakai publik.
 * File bukti bayar (storage) tidak ikut; hanya path-nya yang tercatat.
 *
 * Jalankan: php artisan migrate && php artisan db:seed --class=BookingSnapshotSeeder
 */
class BookingSnapshotSeeder extends Seeder
{
    private const TABLES = ['users', 'tiket', 'pemesanan', 'detail_pemesanan', 'pembayaran'];

    public function run(): void
    {
        $path = database_path('seeders/data/booking-snapshot.json');
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $password = Hash::make(env('SNAPSHOT_USER_PASSWORD', 'Password123'));

        Schema::disableForeignKeyConstraints();
        try {
            DB::transaction(function () use ($data, $password) {
                foreach (array_reverse(self::TABLES) as $table) {
                    DB::table($table)->delete();
                }
                foreach (self::TABLES as $table) {
                    $columns = Schema::getColumnListing($table);
                    foreach (array_chunk($data[$table], 200) as $chunk) {
                        $rows = array_map(function (array $row) use ($table, $columns, $password) {
                            $row = array_intersect_key($row, array_flip($columns));
                            if ($table === 'users') {
                                $row['password'] = $password;
                            }

                            return $row;
                        }, $chunk);
                        DB::table($table)->insert($rows);
                    }
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        foreach (self::TABLES as $table) {
            $this->command?->info($table.': '.DB::table($table)->count());
        }
    }
}
