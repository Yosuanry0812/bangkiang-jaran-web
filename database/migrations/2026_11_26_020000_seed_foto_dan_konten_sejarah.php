<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Foto sejarah (urutan = peran di beranda). */
    private array $photos = [
        ['galeri/sejarah-01.jpg', 'Air terjun bertingkat di antara tebing berlumut, dilihat dari seberang taman.', 'A tiered waterfall between mossy cliffs, seen from across the garden.'],
        ['galeri/sejarah-02.jpg', 'Saung bambu di tengah hutan dan pakis di lembah.', 'A bamboo pavilion among the forest and ferns of the valley.'],
        ['galeri/sejarah-03.jpg', 'Air terjun jatuh di antara batu dan semak dari atas tebing.', 'Water falling between rocks and shrubs, seen from the cliff top.'],
        ['galeri/sejarah-04.jpg', 'Jembatan besi menuju air terjun dan kolam di bawah tebing.', 'An iron bridge leading to the waterfall and the pool below the cliff.'],
    ];

    public function up(): void
    {
        foreach ($this->photos as [$file, $id, $en]) {
            $exists = DB::table('galeri')->where('file', $file)->exists();
            $data = ['keterangan' => $id, 'keterangan_en' => $en, 'tipe' => 'sejarah', 'deleted_at' => null, 'updated_at' => now()];
            if ($exists) {
                DB::table('galeri')->where('file', $file)->update($data);
            } else {
                DB::table('galeri')->insert($data + ['file' => $file, 'created_at' => now()]);
            }
        }

        $isi = implode("\n\n", [
            'Warga Bakbakan menamai air terjun ini dari apa yang mereka lihat setiap hari: tebing cadas di sekelilingnya melengkung seperti punggung kuda. Bangkiang berarti punggung dan jaran berarti kuda, keduanya dalam bahasa Bali.',
            'Cerita ini tidak tertulis di prasasti. Ia sampai ke hari ini lewat tuturan, dari orang tua ke anak. Sebagian orang juga menyebutnya Air Terjun Dedari.',
            'Menurut penuturan warga, sekitar abad ke-14 hutan di perbatasan Gianyar dan Bangli ini menjadi tempat pasukan Kerajaan Gianyar beristirahat selepas bertempur dengan pasukan Kerajaan Bangli. Di dalam hutan masih ada pura tua yang sampai sekarang dipakai warga untuk bersembahyang.',
            'Kawasan ini lama berada di bawah kewenangan adat. Lewat kesepakatan bersama, pengelolaannya diserahkan kepada Pemerintah Desa Bakbakan. Jalan setapak dan jembatan pertama dibangun dari alokasi dana desa sebagai bagian dari program desa wisata.',
            'Air dan hutannya tetap dijaga desa. Mitra dari luar boleh membangun fasilitas, tetapi tidak boleh mengelola air atau merusak lingkungan.',
        ]);
        $isiEn = implode("\n\n", [
            'The people of Bakbakan named this waterfall after what they saw every day: the rock cliffs around it curve like the back of a horse. Bangkiang means back and jaran means horse, both in Balinese.',
            'This story is not carved on any inscription. It has come down by word of mouth, from parents to children. Some also call it Dedari Waterfall.',
            'Villagers say that around the 14th century, this forest on the border of Gianyar and Bangli was where the troops of the Gianyar Kingdom rested after fighting the troops of the Bangli Kingdom. An old temple still stands in the forest, and villagers pray there to this day.',
            'For a long time the area was under customary authority. By common agreement, its management was handed to the Bakbakan Village Government. The first footpath and bridge were built with village funds as part of a village tourism programme.',
            'The village still looks after the water and the forest. Outside partners may build facilities, but they may not manage the water or harm the environment.',
        ]);

        $row = [
            'judul' => 'Sejarah Air Terjun Bangkiang Jaran',
            'judul_en' => 'The Story of Bangkiang Jaran Waterfall',
            'isi' => $isi,
            'isi_en' => $isiEn,
            'updated_at' => now(),
        ];
        if (DB::table('konten')->where('jenis', 'sejarah')->exists()) {
            DB::table('konten')->where('jenis', 'sejarah')->update($row);
        } else {
            DB::table('konten')->insert($row + ['jenis' => 'sejarah', 'created_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('galeri')->where('file', 'like', 'galeri/sejarah-0%')->delete();
    }
};
