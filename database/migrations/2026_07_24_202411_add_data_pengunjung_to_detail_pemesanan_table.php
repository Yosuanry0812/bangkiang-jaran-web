<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            $table->string('nama_pengunjung', 100)->nullable()->after('kode_tiket');
            $table->string('plat_kendaraan', 20)->nullable()->after('nama_pengunjung');
            $table->enum('status_tiket', ['aktif', 'digunakan', 'kadaluarsa'])
                  ->default('aktif')
                  ->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            $table->dropColumn(['nama_pengunjung', 'plat_kendaraan', 'status_tiket']);
        });
    }
};
