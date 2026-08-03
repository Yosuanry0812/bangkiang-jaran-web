<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom password jadi nullable: akun Google tanpa password = harus set password
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        // Akun Google lama password-nya random bcrypt(uniqid()) dari kode lama — reset jadi null
        DB::table('users')->whereNotNull('google_id')->update(['password' => null]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->change();
        });
    }
};
