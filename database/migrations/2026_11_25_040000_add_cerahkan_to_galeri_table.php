<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->boolean('cerahkan')->default(false)->after('di_jalur');
        });

        // Foto yang terlalu gelap: dicerahkan saat ditampilkan (berkas asli tidak diubah).
        DB::table('galeri')->whereIn('file', [
            'galeri/galeri_6b05c82302957.jpg',
            'galeri/galeri_6b05c821ceb8f.jpg',
        ])->update(['cerahkan' => true]);
    }

    public function down(): void
    {
        Schema::table('galeri', fn (Blueprint $table) => $table->dropColumn('cerahkan'));
    }
};
