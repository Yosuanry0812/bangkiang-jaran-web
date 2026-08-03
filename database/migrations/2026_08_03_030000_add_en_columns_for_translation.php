<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->string('nama_tiket_en')->nullable()->after('nama_tiket');
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->string('keterangan_en')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropColumn('nama_tiket_en');
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->dropColumn('keterangan_en');
        });
    }
};
