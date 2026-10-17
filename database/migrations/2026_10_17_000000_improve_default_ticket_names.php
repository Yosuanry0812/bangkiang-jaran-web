<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $renames = [
            'Tiket Masuk Dewasa' => ['Tiket Dewasa', 'Adult Admission'],
            'Tiket Masuk Anak-Anak' => ['Tiket Anak', 'Child Admission'],
            'Tiket Masuk Domestik (WNA)' => ['Tiket Wisatawan Mancanegara', 'International Visitor'],
        ];

        foreach ($renames as $oldName => [$name, $englishName]) {
            DB::table('tiket')
                ->where('nama_tiket', $oldName)
                ->update([
                    'nama_tiket' => $name,
                    'nama_tiket_en' => $englishName,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $renames = [
            'Tiket Dewasa' => 'Tiket Masuk Dewasa',
            'Tiket Anak' => 'Tiket Masuk Anak-Anak',
            'Tiket Wisatawan Mancanegara' => 'Tiket Masuk Domestik (WNA)',
        ];

        foreach ($renames as $currentName => $oldName) {
            DB::table('tiket')
                ->where('nama_tiket', $currentName)
                ->update([
                    'nama_tiket' => $oldName,
                    'nama_tiket_en' => null,
                    'updated_at' => now(),
                ]);
        }
    }
};
