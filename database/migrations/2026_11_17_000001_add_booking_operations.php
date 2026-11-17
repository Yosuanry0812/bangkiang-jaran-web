<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('closed_reason', 30)->nullable();
            $table->uuid('draft_token')->nullable()->unique();
            $table->index(['tgl_kunjungan', 'status'], 'booking_visit_status_index');
        });
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable();
            // Legacy public files are migrated separately, never reinterpreted as private.
            $table->string('proof_disk', 20)->default('public');
            $table->timestamp('verified_at')->nullable();
        });
        Schema::create('booking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pemesanan');
            $table->foreign('id_pemesanan')->references('id_pemesanan')->on('pemesanan')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 40);
            $table->text('description')->nullable();
            $table->timestamp('created_at');
            $table->index(['id_pemesanan', 'created_at']);
        });
        Schema::create('visit_capacities', function (Blueprint $table) {
            $table->date('visit_date')->primary();
            // NULL means unlimited. Zero is not a date-closure feature.
            $table->unsignedInteger('capacity')->nullable();
            $table->timestamps();
        });
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value');
            $table->timestamps();
        });
        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }
        // Historical unpaid bookings get a deadline; receipts awaiting review remain protected.
        DB::table('pemesanan')->where('status', 'pending')->whereNull('expires_at')
            ->update(['expires_at' => now()->addMinutes(120)]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('visit_capacities');
        Schema::dropIfExists('booking_events');
        Schema::table('pembayaran', fn (Blueprint $table) => $table->dropColumn(['rejection_reason', 'proof_disk', 'verified_at']));
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropUnique(['draft_token']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex('booking_visit_status_index');
            $table->dropColumn(['expires_at', 'closed_reason', 'draft_token']);
        });
        Schema::table('users', fn (Blueprint $table) => $table->dropSoftDeletes());
        // jobs may predate this migration and can contain undelivered mail: retain it.
    }
};
