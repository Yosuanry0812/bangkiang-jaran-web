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
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // ========== TIKET ==========
        $tikets = [
            ['nama_tiket' => 'Tiket Masuk Dewasa',     'harga' => 25000, 'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Masuk Anak-Anak',  'harga' => 15000, 'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Paket Keluarga (4 Orang)', 'harga' => 75000, 'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Parkir Motor',     'harga' => 5000,  'status' => 'aktif'],
            ['nama_tiket' => 'Tiket Parkir Mobil',     'harga' => 10000, 'status' => 'aktif'],
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
                'judul' => 'Sejarah Bangkiang Jaran Waterfall',
                'isi'   => "Bangkiang Jaran Waterfall merupakan salah satu air terjun tersembunyi yang terletak di Desa Bakbakan, Kecamatan Gianyar, Kabupaten Gianyar, Bali.\n\nNama \"Bangkiang Jaran\" berasal dari bahasa lokal yang menggambarkan keunikan air terjun ini — \"Bangkiang\" berarti tebing curam, dan \"Jaran\" berarti kuda. Konon, tebing di sekitar air terjun ini memiliki bentuk yang menyerupai kuda.\n\nAir terjun ini memiliki ketinggian sekitar 15 meter dengan kolam alami yang jernih dan segar. Dikelilingi oleh hutan tropis yang masih asri, tempat ini menjadi destinasi favorit bagi wisatawan yang mencari ketenangan dan kesegaran alam.\n\nSejak tahun 2020, pengelolaan objek wisata ini dilakukan secara profesional oleh Pemerintah Desa Bakbakan bersama kelompok sadar wisata (Pokdarwis) setempat untuk memberikan pengalaman terbaik bagi pengunjung.",
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

        // ========== DATA DEMO 12 PEMESANAN + PEMBAYARAN ==========
        $this->call(DemoDataSeeder::class);
    }
}
