<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_tiket');
            $table->date('tgl_kunjungan');
            $table->integer('jumlah');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');
            $table->string('kode_booking', 20)->unique();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_tiket')->references('id_tiket')->on('tiket')->onDelete('cascade');
            $table->index('kode_booking');
            $table->index('status');
            $table->index('tgl_kunjungan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemesanan');
    }
};
