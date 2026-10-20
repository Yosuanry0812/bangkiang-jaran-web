<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('users')->whereNotNull('google_id')->select('google_id')->groupBy('google_id')->havingRaw('COUNT(*) > 1')->exists()
            || DB::table('pembayaran')->select('id_pemesanan')->groupBy('id_pemesanan')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Indeks unik tidak dapat dibuat: terdapat Google ID atau pembayaran ganda. Periksa data terlebih dahulu.');
        }

        if (!Schema::hasIndex('users', 'users_google_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('google_id', 'users_google_id_unique');
            });
        }
        if (!Schema::hasIndex('pembayaran', 'pembayaran_pemesanan_unique')) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->unique('id_pemesanan', 'pembayaran_pemesanan_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropUnique('pembayaran_pemesanan_unique');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_google_id_unique');
        });
    }
};
