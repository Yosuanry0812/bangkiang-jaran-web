<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tiket Paket Keluarga
        DB::table('tiket')
            ->where('nama_tiket', 'Tiket Paket Keluarga (4 Orang)')
            ->delete();

        // Ubah enum kategori — hapus 'paket'
        DB::statement("ALTER TABLE tiket MODIFY COLUMN kategori ENUM('perorangan', 'kendaraan') NOT NULL DEFAULT 'perorangan'");
    }

    public function down(): void
    {
        // Kembalikan enum (tiket yg dihapus tidak dikembalikan otomatis)
        DB::statement("ALTER TABLE tiket MODIFY COLUMN kategori ENUM('perorangan', 'kendaraan', 'paket') NOT NULL DEFAULT 'perorangan'");
    }
};
