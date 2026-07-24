<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pemesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_pemesanan');
            $table->unsignedBigInteger('id_tiket');
            $table->string('kode_tiket', 20);
            $table->string('nama_tiket', 100);
            $table->integer('harga');
            $table->timestamps();

            $table->foreign('id_pemesanan')->references('id_pemesanan')->on('pemesanan')->onDelete('cascade');
            $table->foreign('id_tiket')->references('id_tiket')->on('tiket')->onDelete('cascade');
            $table->unique('kode_tiket');
            $table->index('id_pemesanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanan');
    }
};
