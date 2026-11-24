<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Keterangan untuk foto galeri awal; hanya mengisi yang masih kosong. */
    private array $captions = [
        ['galeri/galeri_6b05c81d19cbe.jpg', 'Air terjun jatuh ke kolam yang dikelilingi pakis.', 'A waterfall falling into a pool framed by ferns.'],
        ['galeri/galeri_6b05c81ed1543.jpg', 'Air terjun jatuh ke kolam yang dikelilingi pakis.', 'A waterfall falling into a pool framed by ferns.'],
        ['galeri/galeri_6b05c81dabd16.jpg', 'Saung bambu dengan meja makan menghadap lembah hijau.', 'A bamboo pavilion with a dining table facing the green valley.'],
        ['galeri/galeri_6b05c81e3cb19.jpg', 'Aliran sungai di dasar lembah yang rimbun.', 'The river running through the leafy valley.'],
        ['galeri/galeri_6b05c81f7621c.jpg', 'Aliran sungai di dasar lembah yang rimbun.', 'The river running through the leafy valley.'],
        ['galeri/galeri_6b05c820152e3.jpg', 'Pancuran kecil di antara daun pakis jatuh ke kolam.', 'A thin stream falling into a pool among ferns.'],
        ['galeri/galeri_6b05c82385e96.jpg', 'Pancuran kecil di antara daun pakis jatuh ke kolam.', 'A thin stream falling into a pool among ferns.'],
        ['galeri/galeri_6b05c820a761f.jpg', 'Tangga batu berlumut dan akar pohon besar di tepi kolam.', 'Mossy stone steps and tree roots at the pool’s edge.'],
    ];

    public function up(): void
    {
        foreach ($this->captions as [$file, $id, $en]) {
            DB::table('galeri')->where('file', $file)
                ->where(fn ($q) => $q->whereNull('keterangan')->orWhere('keterangan', ''))
                ->update(['keterangan' => $id, 'keterangan_en' => $en]);
        }
    }

    public function down(): void {}
};
