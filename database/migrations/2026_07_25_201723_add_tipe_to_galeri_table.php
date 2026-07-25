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
        Schema::table('galeri', function (Blueprint $table) {
            if (!Schema::hasColumn('galeri', 'tipe')) {
                $table->enum('tipe', ['wisata', 'restoran'])->default('wisata')->after('file');
            }
        });

        // Set existing records as 'wisata'
        DB::statement("UPDATE galeri SET tipe='wisata' WHERE tipe IS NULL");
    }

    public function down(): void
    {
        Schema::table('galeri', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
