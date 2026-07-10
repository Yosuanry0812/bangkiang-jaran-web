<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->unsignedBigInteger('id_pemesanan');
            $table->decimal('total', 10, 2);
            $table->string('metode', 50);
            $table->string('bukti_bayar', 255)->nullable();
            $table->enum('status', ['pending', 'valid', 'ditolak'])->default('pending');
            $table->date('tgl_bayar')->nullable();
            $table->timestamps();

            $table->foreign('id_pemesanan')->references('id_pemesanan')->on('pemesanan')->onDelete('cascade');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
