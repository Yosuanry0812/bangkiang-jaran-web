<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $wisatawanId = DB::table('users')->where('email', 'wisatawan@demo.com')->value('id');
        if (!$wisatawanId) {
            echo "Jalankan DatabaseSeeder dulu (user wisatawan@demo.com belum ada).\n";
            return;
        }

        // Hapus data demo lama — cari berdasarkan id_user wisatawan demo
        $oldIds = DB::table('pemesanan')->where('id_user', $wisatawanId)->pluck('id_pemesanan');
        if ($oldIds->count()) {
            DB::table('pembayaran')->whereIn('id_pemesanan', $oldIds)->delete();
            DB::table('pemesanan')->whereIn('id_pemesanan', $oldIds)->delete();
        }

        $today = now()->toDateString();

        // Helper generate kode booking real (spt di PemesananController)
        $gen = function () {
            return 'BJ-' . strtoupper(substr(uniqid(), -8));
        };

        // Pastikan unik
        $codes = [];
        while (count($codes) < 12) {
            $c = $gen();
            if (!in_array($c, $codes)) $codes[] = $c;
        }

        // ===== 12 DATA PEMESANAN =====
        $pemesanan = [
            // 1. Belum bayar
            ['kode_booking' => $codes[0],  'id_tiket' => 1, 'jumlah' => 2, 'total_harga' => 50000,  'tgl_kunjungan' => now()->addDays(3)->toDateString(), 'status' => 'pending',   'created' => now()->subDays(5)],
            // 2. Selesai + valid
            ['kode_booking' => $codes[1],  'id_tiket' => 3, 'jumlah' => 1, 'total_harga' => 75000,  'tgl_kunjungan' => now()->addDays(7)->toDateString(), 'status' => 'selesai',   'created' => now()->subDays(4)],
            // 3. Diproses + pending payment (VERIFIKASI)
            ['kode_booking' => $codes[2],  'id_tiket' => 1, 'jumlah' => 3, 'total_harga' => 75000,  'tgl_kunjungan' => $today,                              'status' => 'diproses',  'created' => now()->subHours(12)],
            // 4. Diproses + pending payment (VERIFIKASI)
            ['kode_booking' => $codes[3],  'id_tiket' => 2, 'jumlah' => 2, 'total_harga' => 30000,  'tgl_kunjungan' => now()->addDay()->toDateString(),       'status' => 'diproses',  'created' => now()->subHours(10)],
            // 5. Selesai + valid
            ['kode_booking' => $codes[4],  'id_tiket' => 4, 'jumlah' => 1, 'total_harga' => 5000,   'tgl_kunjungan' => now()->subDay()->toDateString(),       'status' => 'selesai',   'created' => now()->subDays(3)],
            // 6. Selesai + valid
            ['kode_booking' => $codes[5],  'id_tiket' => 1, 'jumlah' => 4, 'total_harga' => 100000, 'tgl_kunjungan' => now()->subDays(2)->toDateString(),     'status' => 'selesai',   'created' => now()->subDays(3)],
            // 7. Selesai + valid
            ['kode_booking' => $codes[6],  'id_tiket' => 3, 'jumlah' => 1, 'total_harga' => 75000,  'tgl_kunjungan' => now()->subDays(3)->toDateString(),     'status' => 'selesai',   'created' => now()->subDays(4)],
            // 8. Dibatalkan + payment ditolak
            ['kode_booking' => $codes[7],  'id_tiket' => 2, 'jumlah' => 3, 'total_harga' => 45000,  'tgl_kunjungan' => now()->addDays(5)->toDateString(),     'status' => 'dibatalkan','created' => now()->subDays(2)],
            // 9. Selesai + valid
            ['kode_booking' => $codes[8],  'id_tiket' => 5, 'jumlah' => 1, 'total_harga' => 10000,  'tgl_kunjungan' => now()->subDays(4)->toDateString(),     'status' => 'selesai',   'created' => now()->subDays(5)],
            // 10. Belum bayar
            ['kode_booking' => $codes[9],  'id_tiket' => 1, 'jumlah' => 2, 'total_harga' => 50000,  'tgl_kunjungan' => now()->addDays(10)->toDateString(),    'status' => 'pending',   'created' => now()->subHours(6)],
            // 11. Diproses + pending payment (VERIFIKASI)
            ['kode_booking' => $codes[10], 'id_tiket' => 3, 'jumlah' => 2, 'total_harga' => 150000, 'tgl_kunjungan' => now()->addDays(2)->toDateString(),     'status' => 'diproses',  'created' => now()->subHours(4)],
            // 12. Dibatalkan (sebelum bayar)
            ['kode_booking' => $codes[11], 'id_tiket' => 1, 'jumlah' => 1, 'total_harga' => 25000,  'tgl_kunjungan' => now()->subDays(5)->toDateString(),     'status' => 'dibatalkan','created' => now()->subDays(6)],
        ];

        $insertedIds = [];
        DB::beginTransaction();
        try {
            foreach ($pemesanan as $p) {
                $id = DB::table('pemesanan')->insertGetId([
                    'id_user'       => $wisatawanId,
                    'id_tiket'      => $p['id_tiket'],
                    'tgl_kunjungan' => $p['tgl_kunjungan'],
                    'jumlah'        => $p['jumlah'],
                    'total_harga'   => $p['total_harga'],
                    'status'        => $p['status'],
                    'kode_booking'  => $p['kode_booking'],
                    'created_at'    => $p['created'],
                    'updated_at'    => $p['created'],
                ]);
                $insertedIds[$p['kode_booking']] = $id;
            }

            // ===== PEMBAYARAN =====
            $pembayaran = [
                ['kb' => $codes[1],  'total' => 75000,  'metode' => 'Transfer BRI',         'status' => 'valid',   'tgl_bayar' => now()->subDays(4)->toDateString(),  'created' => now()->subDays(4)],
                ['kb' => $codes[2],  'total' => 75000,  'metode' => 'Transfer BCA',         'status' => 'pending', 'tgl_bayar' => now()->subHours(12)->toDateString(),'created' => now()->subHours(12), 'bukti' => 'bukti/bayar1.jpg'],
                ['kb' => $codes[3],  'total' => 30000,  'metode' => 'Dana',                 'status' => 'pending', 'tgl_bayar' => now()->subHours(10)->toDateString(),'created' => now()->subHours(10), 'bukti' => 'bukti/bayar2.jpg'],
                ['kb' => $codes[4],  'total' => 5000,   'metode' => 'Transfer BRI',         'status' => 'valid',   'tgl_bayar' => now()->subDays(3)->toDateString(),  'created' => now()->subDays(3)],
                ['kb' => $codes[5],  'total' => 100000, 'metode' => 'GoPay',                'status' => 'valid',   'tgl_bayar' => now()->subDays(3)->toDateString(),  'created' => now()->subDays(3)],
                ['kb' => $codes[6],  'total' => 75000,  'metode' => 'Transfer Mandiri',     'status' => 'valid',   'tgl_bayar' => now()->subDays(4)->toDateString(),  'created' => now()->subDays(4)],
                ['kb' => $codes[7],  'total' => 45000,  'metode' => 'Transfer BRI',         'status' => 'ditolak', 'tgl_bayar' => now()->subDays(2)->toDateString(),  'created' => now()->subDays(2)],
                ['kb' => $codes[8],  'total' => 10000,  'metode' => 'QRIS OVO',             'status' => 'valid',   'tgl_bayar' => now()->subDays(5)->toDateString(),  'created' => now()->subDays(5)],
                ['kb' => $codes[10], 'total' => 150000, 'metode' => 'Transfer BNI',         'status' => 'pending', 'tgl_bayar' => now()->subHours(4)->toDateString(), 'created' => now()->subHours(4), 'bukti' => 'bukti/bayar3.jpg'],
            ];

            foreach ($pembayaran as $b) {
                $pemesananId = $insertedIds[$b['kb']] ?? null;
                if (!$pemesananId) continue;

                DB::table('pembayaran')->insert([
                    'id_pemesanan' => $pemesananId,
                    'total'        => $b['total'],
                    'metode'       => $b['metode'],
                    'bukti_bayar'  => $b['bukti'] ?? null,
                    'status'       => $b['status'],
                    'tgl_bayar'    => $b['tgl_bayar'],
                    'created_at'   => $b['created'],
                    'updated_at'   => $b['created'],
                ]);
            }

            DB::commit();
            echo "✓ 12 pemesanan + 9 pembayaran berhasil dibuat.\n";
            echo "  Kode booking: " . implode(', ', $codes) . "\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "✗ Gagal: " . $e->getMessage() . "\n";
        }
    }
}
