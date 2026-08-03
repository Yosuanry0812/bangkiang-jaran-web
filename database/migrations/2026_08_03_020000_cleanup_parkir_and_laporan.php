<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel laporan tidak pernah dipakai (laporan dibuat on-the-fly) — hapus
        Schema::dropIfExists('laporan');

        // Hapus tiket parkir: pindahkan dulu pemesanan yang mereferensikannya ke tiket masuk dewasa
        $dewasa = DB::table('tiket')->where('nama_tiket', 'Tiket Masuk Dewasa')->first();
        if ($dewasa) {
            $parkirIds = DB::table('tiket')
                ->where('nama_tiket', 'LIKE', 'Tiket Parkir%')
                ->pluck('id_tiket');

            if ($parkirIds->isNotEmpty()) {
                DB::table('pemesanan')
                    ->whereIn('id_tiket', $parkirIds)
                    ->update([
                        'id_tiket'    => $dewasa->id_tiket,
                        'total_harga' => DB::raw($dewasa->harga . ' * jumlah'),
                    ]);

                DB::table('tiket')->whereIn('id_tiket', $parkirIds)->delete();
            }
        }
    }

    public function down(): void
    {
        // Tidak ada rollback data — parkir tidak di-restore
    }
};
