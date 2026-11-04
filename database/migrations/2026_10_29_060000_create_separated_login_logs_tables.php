<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel khusus riwayat Login Sukses
        if (!Schema::hasTable('log_login_sukses')) {
            Schema::create('log_login_sukses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user')->nullable()->index();
                $table->string('nama', 150)->nullable();
                $table->string('email', 150)->index();
                $table->string('role', 50)->nullable();
                $table->string('metode', 50)->default('manual'); // 'manual', 'google'
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 2. Buat tabel khusus riwayat Login Gagal
        if (!Schema::hasTable('log_login_gagal')) {
            Schema::create('log_login_gagal', function (Blueprint $table) {
                $table->id();
                $table->string('input_login', 150)->index();
                $table->string('alasan', 255)->default('Kredensial tidak cocok');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 3. Rapikan data users yang mendaftar via Google (User lama yang status verifikasinya masih NULL)
        DB::table('users')
            ->whereNotNull('google_id')
            ->where('google_id', '!=', '')
            ->update([
                'registration_method' => 'google',
                'email_verification_method' => 'google',
            ]);

        DB::table('users')
            ->where('registration_method', 'google')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => DB::raw('created_at'),
            ]);

        // 4. Migrasikan data riwayat login dari tabel lama activity_logs ke tabel masing-masing
        if (Schema::hasTable('activity_logs')) {
            $successLogs = DB::table('activity_logs')
                ->where('aktivitas', 'Login sukses')
                ->get();

            foreach ($successLogs as $log) {
                $email = trim(str_replace('Login sebagai', '', $log->detail ?? ''));
                $user = null;
                if ($log->id_user) {
                    $user = DB::table('users')->where('id', $log->id_user)->first();
                }
                if (!$user && $email !== '') {
                    $user = DB::table('users')->where('email', $email)->first();
                }

                DB::table('log_login_sukses')->insert([
                    'id_user'    => $user?->id ?? $log->id_user,
                    'nama'       => $user?->name ?? ($email ?: 'Pengguna'),
                    'email'      => $user?->email ?? ($email ?: '-'),
                    'role'       => $user?->role ?? 'wisatawan',
                    'metode'     => $user?->last_login_method ?? 'manual',
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'created_at' => $log->created_at ?? now(),
                    'updated_at' => $log->updated_at ?? now(),
                ]);
            }

            $failedLogs = DB::table('activity_logs')
                ->where('aktivitas', 'Login gagal')
                ->get();

            foreach ($failedLogs as $log) {
                $input = trim(str_replace('Percobaan login gagal untuk:', '', $log->detail ?? ''));

                DB::table('log_login_gagal')->insert([
                    'input_login' => $input ?: '-',
                    'alasan'      => 'Password salah atau akun tidak ditemukan',
                    'ip_address'  => $log->ip_address,
                    'user_agent'  => $log->user_agent,
                    'created_at'  => $log->created_at ?? now(),
                    'updated_at'  => $log->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('log_login_sukses');
        Schema::dropIfExists('log_login_gagal');
    }
};
