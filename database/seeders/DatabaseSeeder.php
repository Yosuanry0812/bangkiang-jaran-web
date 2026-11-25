<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ========== AKUN DEMO ==========
        DB::table('users')->updateOrInsert(
            ['email' => 'pengelola@bangkiangjaran.com'],
            [
                'name'              => 'Pengelola Bangkiang Jaran',
                'username'          => 'pengelola',
                'phone'             => '081234567890',
                'password'          => Hash::make('Password123'),
                'role'              => 'pengelola',
                'email_verified_at' => now(),
                'last_login_at'     => now()->subHours(2),
                'last_login_method' => 'manual',
                'login_count'       => 3,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'wisatawan@demo.com'],
            [
                'name'              => 'Wisatawan Demo',
                'username'          => 'wisatawan',
                'phone'             => '081234567891',
                'password'          => Hash::make('Password123'),
                'role'              => 'wisatawan',
                'email_verified_at' => now(),
                'last_login_at'     => now()->subDay(),
                'last_login_method' => 'manual',
                'login_count'       => 5,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // ========== TIKET ==========
        $tikets = [
            ['nama_tiket' => 'Tiket Dewasa',                  'nama_tiket_en' => 'Adult Admission',       'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Anak',                     'nama_tiket_en' => 'Child Admission',       'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Wisatawan Mancanegara',   'nama_tiket_en' => 'International Visitor', 'harga' => 30000, 'kategori' => 'perorangan', 'status' => 'aktif'],
        ];
        foreach ($tikets as $t) {
            DB::table('tiket')->updateOrInsert(
                ['nama_tiket' => $t['nama_tiket']],
                array_merge($t, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ========== KONTEN ==========
        $kontens = [
            [
                'judul' => 'Sejarah Air Terjun Bangkiang Jaran',
                'isi'   => "Warga Bakbakan menamai air terjun ini dari apa yang mereka lihat setiap hari: tebing cadas di sekelilingnya melengkung seperti punggung kuda. Bangkiang berarti punggung dan jaran berarti kuda, keduanya dalam bahasa Bali.\n\nCerita ini tidak tertulis di prasasti. Ia sampai ke hari ini lewat tuturan, dari orang tua ke anak. Sebagian orang juga menyebutnya Air Terjun Dedari.\n\nMenurut penuturan warga, sekitar abad ke-14 hutan di perbatasan Gianyar dan Bangli ini menjadi tempat pasukan Kerajaan Gianyar beristirahat selepas bertempur dengan pasukan Kerajaan Bangli. Di dalam hutan masih ada pura tua yang sampai sekarang dipakai warga untuk bersembahyang.\n\nKawasan ini lama berada di bawah kewenangan adat. Lewat kesepakatan bersama, pengelolaannya diserahkan kepada Pemerintah Desa Bakbakan. Jalan setapak dan jembatan pertama dibangun dari alokasi dana desa sebagai bagian dari program desa wisata.\n\nAir dan hutannya tetap dijaga desa. Mitra dari luar boleh membangun fasilitas, tetapi tidak boleh mengelola air atau merusak lingkungan.",
                'jenis'  => 'sejarah',
            ],
            [
                'judul' => 'Informasi Umum',
                'isi'   => "Jam Operasional:\n• Senin - Minggu: 07.00 - 18.00 WITA\n\nFasilitas:\n• Area parkir luas (motor & mobil)\n• Toilet umum\n• Warung makan & minuman\n• Gazebo untuk bersantai\n• Spot foto instagramable\n• Guide lokal (opsional)\n\nTips Berkunjung:\n• Gunakan pakaian dan alas kaki yang nyaman\n• Bawa pakaian ganti jika ingin berenang\n• Jaga kebersihan dengan tidak membuang sampah sembarangan\n• Patuhi arahan petugas untuk keselamatan",
                'jenis'  => 'info',
            ],
        ];
        foreach ($kontens as $k) {
            DB::table('konten')->updateOrInsert(
                ['judul' => $k['judul']],
                array_merge($k, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ========== GALERI ==========
        $galeris = [
            'Air terjun Bangkiang Jaran dari kejauhan',
            'Suasana hutan tropis sekitar air terjun',
            'Kolam alami yang jernih dan segar',
            'Spot foto instagramable di area wisata',
            'Gazebo untuk bersantai menikmati alam',
            'Jalur trekking menuju air terjun',
            'Pemandangan matahari terbenam dari atas tebing',
            'Area parkir yang luas dan nyaman',
        ];
        foreach ($galeris as $i => $ket) {
            $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            DB::table('galeri')->updateOrInsert(
                ['file' => 'galeri/galeri-' . $num . '.jpg'],
                [
                    'file'       => 'galeri/galeri-' . $num . '.jpg',
                    'keterangan' => $ket,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ========== DATA DEMO 12 PEMESANAN + PEMBAYARAN (OPSIONAL) ==========
        // $this->call(DemoDataSeeder::class);
    }
}
