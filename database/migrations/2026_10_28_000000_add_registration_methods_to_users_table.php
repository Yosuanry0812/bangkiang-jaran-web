<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_method', 16)->default('email')->index();
            $table->string('email_verification_method', 16)->nullable();
        });

        DB::table('users')->whereNotNull('google_id')->where('google_id', '!=', '')
            ->update(['registration_method' => 'google']);
        DB::table('users')->where('registration_method', 'google')->whereNotNull('email_verified_at')
            ->update(['email_verification_method' => 'google']);
        // Legacy email accounts retain their real verification status; never invent an OTP check.
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['registration_method']);
            $table->dropColumn(['registration_method', 'email_verification_method']);
        });
    }
};
