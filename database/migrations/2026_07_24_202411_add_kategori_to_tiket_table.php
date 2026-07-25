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
        Schema::table('tiket', function (Blueprint $table) {
            $table->enum('kategori', ['perorangan', 'kendaraan'])
                  ->default('perorangan')
                  ->after('harga');
        });

        // Set default kategori for existing tickets
        DB::statement("UPDATE tiket SET kategori='perorangan' WHERE nama_tiket LIKE '%Dewasa%' OR nama_tiket LIKE '%Anak%'");
        DB::statement("UPDATE tiket SET kategori='kendaraan' WHERE nama_tiket LIKE '%Parkir%' OR nama_tiket LIKE '%Motor%' OR nama_tiket LIKE '%Mobil%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
