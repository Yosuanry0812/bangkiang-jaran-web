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
            $table->enum('tipe', ['wisata', 'restoran', 'sejarah'])->default('wisata')->change();
        });
    }

    public function down(): void
    {
        DB::table('galeri')->where('tipe', 'sejarah')->update(['tipe' => 'wisata']);
        Schema::table('galeri', function (Blueprint $table) {
            $table->enum('tipe', ['wisata', 'restoran'])->default('wisata')->change();
        });
    }
};
