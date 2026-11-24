<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Foto jalur di beranda yang dikunci beserta keterangannya (urutan tampil: atas ke bawah). */
    private array $trail = [
        ['galeri/galeri_6b05c82385e96.jpg', null, null],
        ['galeri/galeri_6b05c82302957.jpg', 'Air terjun kecil di ceruk batu yang teduh dan gelap.', 'A slim waterfall in a shaded rocky grotto.'],
        ['galeri/galeri_6b05c8226aa3a.jpg', 'Dua air terjun dan anak tangga batu di tengah rimbunnya pakis.', 'Two waterfalls and stone steps among dense ferns.'],
        ['galeri/galeri_6b05c821ceb8f.jpg', 'Aliran deras air terjun menuruni tebing berbatu.', 'Rushing water pouring down the rocky cliff.'],
        ['galeri/galeri_6b05c821395a8.jpg', 'Air terjun tinggi dengan arca penjaga di tepi kolam.', 'A tall waterfall with a guardian statue by the pool.'],
    ];

    public function up(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->boolean('di_jalur')->default(false)->after('tipe');
        });

        foreach ($this->trail as [$file, $id, $en]) {
            DB::table('galeri')->where('file', $file)->update(['di_jalur' => true]);
            if ($id) {
                DB::table('galeri')->where('file', $file)
                    ->where(fn ($q) => $q->whereNull('keterangan')->orWhere('keterangan', ''))
                    ->update(['keterangan' => $id, 'keterangan_en' => $en]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('galeri', fn (Blueprint $table) => $table->dropColumn('di_jalur'));
    }
};
