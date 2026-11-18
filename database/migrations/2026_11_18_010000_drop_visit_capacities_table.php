<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fitur kuota kunjungan dihapus; tabel batas kapasitas per tanggal tidak lagi dipakai.
     */
    public function up(): void
    {
        Schema::dropIfExists('visit_capacities');
    }

    public function down(): void
    {
        if (Schema::hasTable('visit_capacities')) {
            return;
        }

        Schema::create('visit_capacities', function (Blueprint $table) {
            $table->date('visit_date')->primary();
            $table->unsignedInteger('capacity')->nullable();
            $table->timestamps();
        });
    }
};
