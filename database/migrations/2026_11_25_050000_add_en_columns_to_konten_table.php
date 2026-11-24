<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('konten')) {
            return;
        }
        Schema::table('konten', function (Blueprint $table) {
            if (! Schema::hasColumn('konten', 'judul_en')) {
                $table->string('judul_en')->nullable()->after('judul');
            }
            if (! Schema::hasColumn('konten', 'isi_en')) {
                $table->text('isi_en')->nullable()->after('isi');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('konten')) {
            return;
        }
        Schema::table('konten', function (Blueprint $table) {
            foreach (['judul_en', 'isi_en'] as $column) {
                if (Schema::hasColumn('konten', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
