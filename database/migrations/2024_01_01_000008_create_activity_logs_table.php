<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('aktivitas', 100);
            $table->text('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
            $table->index('aktivitas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
