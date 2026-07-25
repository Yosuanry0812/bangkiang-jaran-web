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
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_pemesanan', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nama_pengunjung');
            }
            if (!Schema::hasColumn('detail_pemesanan', 'check_in_at')) {
                $table->timestamp('check_in_at')->nullable()->after('status_tiket');
            }
            if (!Schema::hasColumn('detail_pemesanan', 'check_out_at')) {
                $table->timestamp('check_out_at')->nullable()->after('check_in_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'check_in_at', 'check_out_at']);
        });
    }
};
